<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\IssueModel;
use App\Models\VolumeModel;
use App\Models\ReferenceModel;

class MainController extends BaseController
{
    protected $articleModel;
    protected $issueModel;
    protected $volumeModel;

    protected $referenceModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        $this->issueModel = new IssueModel();
        $this->volumeModel = new VolumeModel();
        $this->referenceModel = new ReferenceModel();

    }
    public function index()
    {
        $data = [
            'title' => 'Home Page',
            'content' => 'index',
        ];
        return view('layout/templates', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'About the Journal',
            'content' => 'about',
        ];
        return view('layout/templates', $data);
    }

    public function currentIssue()
    {
        // Step 1: Get the latest article with its issue details
        $firstArticle = $this->articleModel
            ->select('articles.*, issues.published_date, issues.issue_no, issues.issue_type, issues.issue_image, issues.issue_pdf, volumes.volume_no, volumes.year')
            ->join('issues', 'issues.id = articles.issue_id')
            ->join('volumes', 'volumes.id = issues.volume_id')
            ->orderBy('issues.published_date', 'ASC')
            ->orderBy('articles.created_at', 'ASC')
            ->first();
        // If no article found
        if (!$firstArticle) {
            return view('layout/templates', [
                'title' => 'Current Issue',
                'content' => 'current_issue',
                'articles' => [],
                'issue' => null
            ]);
        }

        $issueId = $firstArticle['issue_id'];

        // Step 2: Get all articles from that issue
        $articles = $this->articleModel
            ->select('articles.*, issues.published_date, issues.issue_no, issues.issue_type, issues.issue_image, issues.issue_pdf, volumes.volume_no, volumes.year')
            ->join('issues', 'issues.id = articles.issue_id')
            ->join('volumes', 'volumes.id = issues.volume_id')
            ->where('articles.issue_id', $issueId)
            ->orderBy('articles.created_at', 'ASC')
            ->findAll();

        return view('layout/templates', [
            'title' => 'Current Issue',
            'content' => 'current_issue',
            'articles' => $articles,
            'issue' => $firstArticle // contains all issue-related info
        ]);
    }




    public function detail($id)
    {
        $article = $this->articleModel
            ->select('articles.*, issues.issue_no, volumes.volume_no, issues.published_date')
            ->join('issues', 'issues.id = articles.issue_id')
            ->join('volumes', 'volumes.id = issues.volume_id')
            ->find($id);

        if (!$article) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Article not found.");
        }
        $references = $this->referenceModel->where('article_id', $id)->orderBy('ref_no')->findAll();

        $data = [
            'title' => $article['title'],
            'content' => 'article_detail',
            'article' => $article,
            'references' => $references
        ];

        return view('layout/templates', $data);
    }


    public function issues()
    {
        // Group volumes & issues by year (like second screenshot)
        $volumeModel = new \App\Models\VolumeModel();
        $issueModel = new \App\Models\IssueModel();

        $volumes = $volumeModel->orderBy('year', 'DESC')->findAll();

        foreach ($volumes as &$volume) {
            $volume['issues'] = $issueModel->where('volume_id', $volume['id'])->orderBy('issue_no')->findAll();
        }

        $data = [
            'title' => 'Past Issues',
            'content' => 'issues',
            'volumes' => $volumes,
        ];

        return view('layout/templates', $data);
    }

    public function specialIssues()
    {
        $articles = $this->articleModel
            ->select('articles.*, issues.published_date, issues.issue_no, issues.issue_image, issues.issue_pdf, issues.issue_type, volumes.volume_no, volumes.year')
            ->join('issues', 'issues.id = articles.issue_id')
            ->join('volumes', 'volumes.id = issues.volume_id')
            ->where('issues.issue_type', 'special')
            ->orderBy('issues.published_date', 'ASC')
            ->orderBy('articles.created_at', 'ASC')
            ->findAll();

        // Group articles by issue
        $groupedIssues = [];
        foreach ($articles as $article) {
            $groupedIssues[$article['issue_id']]['issue'] = [
                'published_date' => $article['published_date'],
                'issue_no' => $article['issue_no'],
                'issue_image' => $article['issue_image'],
                'issue_pdf' => $article['issue_pdf'],
                'issue_type' => $article['issue_type'],
                'volume_no' => $article['volume_no'],
                'year' => $article['year'],
            ];
            $groupedIssues[$article['issue_id']]['articles'][] = $article;
        }

        return view('layout/templates', [
            'title' => 'Special Issues',
            'content' => 'special-issues',
            'groupedIssues' => $groupedIssues
        ]);
    }



    public function issueDetail($id)
    {
        // Get issue details with volume information
        $issue = $this->issueModel
            ->select('issues.*, volumes.volume_no, volumes.year')
            ->join('volumes', 'volumes.id = issues.volume_id')
            ->where('issues.id', $id)
            ->first();

        if (!$issue) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Issue not found.');
        }

        // Get all articles under this issue
        $articles = $this->articleModel
            ->select('articles.*, volumes.volume_no, issues.issue_no, issues.published_date')
            ->join('issues', 'issues.id = articles.issue_id')
            ->join('volumes', 'volumes.id = issues.volume_id')
            ->where('articles.issue_id', $id)
            ->orderBy('articles.id', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Volume ' . $issue['volume_no'] . ', Issue ' . $issue['issue_no'],
            'issue' => $issue,
            'articles' => $articles,
            'content' => 'issue_detail', // this should be your view file
        ];

        return view('layout/templates', $data);
    }



    public function aimScope()
    {
        $data = [
            'title' => 'Aim & Scope',
            'content' => 'aimscope',
        ];
        return view('layout/templates', $data);
    }

    public function editorialBoard()
    {
        $data = [
            'title' => 'Editorial Board',
            'content' => 'editorial-board',
        ];
        return view('layout/templates', $data);
    }

    // public function specialIssues()
    // {
    //     $data = [
    //         'title' => 'Special Issues',
    //         'content' => 'special-issues',
    //     ];
    //     return view('layout/templates', $data);
    // }

    public function contact()
    {
        $data = [
            'title' => 'Contact Us',
            'content' => 'contact',
        ];
        return view('layout/templates', $data);
    }

    public function articles()
    {
        $data = [
            'title' => 'Articles',
            'content' => 'articles',
        ];
        return view('layout/templates', $data);
    }

    public function articleDetail($id)
    {
        // Fetch article detail from model later
        $data = [
            'title' => 'Article Detail',
            'content' => 'article-detail',
            'article_id' => $id
        ];
        return view('layout/templates', $data);
    }
}
