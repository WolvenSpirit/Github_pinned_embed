<?php
# Embeddable scrape of your pinned repositories, use include to append it in your view.
# Modify the variable on line 46 to point to your profile.
# In the near future if github has ui overhaul it may need the xpath changed to point again to the right section of the page.
# enjoy - WolvenSpirit
class GithubScrape
{
    protected $user;
    protected $base_url = "https://github.com/";
    protected $full_url;
    protected $all_repo_url;
    public function init($github_user)
    {
      $this->user = $github_user;
      $this->full_url = $this->base_url.$github_user;
    }
    public function fetch_profile()
    {
      $fake_header = array(
        'Content-Type'=>'application/x-www-form-urlencoded',
        'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
        'Accept-Charset'=>'utf-8'
                          );
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->full_url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $fake_header);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      return curl_exec($ch);
    }
    public function find_pinned_repositories($scraped_page)
    {
      $doc = new DOMDocument();
      $doc->loadHTML($scraped_page);
      $doc_xpath = new DOMXPath($doc);
      $pinned_repositories = $doc_xpath->query('//body/div[4]/div/div/div[3]/div[2]/div/div');
      # pinned repositories is returned as a dom node list.. iterable.
      # dom node list is pushed to array stack after being appended to the newly created DOMDocument and imported as node.
      $DOM_view = new DOMDocument();

      $parsedDOM = array();
      foreach ($pinned_repositories as $item)
      {
          $DOM_view->appendChild($DOM_view->importNode($item, true));
          array_push($parsedDOM, $DOM_view->saveHTML());
      }

      return $parsedDOM;
    }
    public function fetch_all() # This is all in one, sorry.
    {
      $this->all_repo_url = $this->full_url."?tab=repositories";
      $fake_header = array(
        'Content-Type'=>'application/x-www-form-urlencoded',
        'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
        'Accept-Charset'=>'utf-8'
                          );
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->all_repo_url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $fake_header);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $data = curl_exec($ch);


      $doc = new DOMDocument();
      $doc->loadHTML($data);
      $doc_xpath = new DOMXPath($doc);
      $all_repositories = $doc_xpath->query('//body/div[4]/div/div/div[3]/div[2]/div[2]');
      # pinned repositories is returned as a dom node list.. iterable.
      # dom node list is pushed to array stack after being appended to the newly created DOMDocument and imported as node.
      $DOM_view = new DOMDocument();

      $parsedDOM = array();
      foreach ($all_repositories as $item)
      {
          $DOM_view->appendChild($DOM_view->importNode($item, true));
          array_push($parsedDOM, $DOM_view->saveHTML());
      }

      return $parsedDOM;
    }
}
$github_user = "WolvenSpirit";
$user = new GithubScrape();
$user->init($github_user);
