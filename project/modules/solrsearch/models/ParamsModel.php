<?php

namespace project\modules\solrsearch\models;

use craft\base\Model;
use wsydney76\solrsearch\models\SearchParamsModel;

class ParamsModel extends SearchParamsModel
{
    public $facet = 'on';
    public $facetField = ['releaseyear', 'sectiontitle_exact', 'acting_exact', 'role_exact','type','genre_exact','country_exact', 'crew_exact'];
    public $fl = 'score,key,id,site,type,slug,url,title,name,film,acting,role,sectiontitle,releaseyear,imagefile,genre,country,crew';
    public $hl = 'on';
    public $hlFl = 'title,name,film,acting,role,crew,genre,country,sectiontitle,releaseyear,content,station,remark';
    public $hlSimplePost = '</i></b>';
    public $hlSimplePre = '<b><i>';
    public $mm = '1<-2 6<70%';
    public $pf = 'title^10.0 content^2.0 site^1.0 genre^05 country^0.5 film^1.0 acting^1.0 role^1.0 crew^1.0 sectiontitle^1.0 releaseyear^1.0';
    public $q = '';
    public $qf = 'title^10.0 content^2.0 site^1.0 genre^05 country^0.5 film^1.0 acting^1.0 role^1.0 crew^1.0 sectiontitle^1.0 releaseyear^1.0';
    public $sort = 'score desc,title_sort asc';
    public $spellcheck = 'true';
    public $spellcheckCollate = 'true';
    public $spellcheckExtendedResults = 'true';
}
