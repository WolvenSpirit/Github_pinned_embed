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
}
$github_user = "WolvenSpirit";
$user = new GithubScrape();
$user->init($github_user);
$user_page = $user->fetch_profile();
$pinned_repositories = $user->find_pinned_repositories($user_page);

# Needs preg_replace to prepend the base_url to the links.
$pinned = preg_replace("/href=\"/", "/target=blank href=\"https://github.com/", $pinned_repositories[0]);
# Modify the echo'd style or give the div an id and modify from your global css.
echo "<div class='container' style='color:darkslategrey;font-size:.9em;padding:.5em;box-shadow:4px 4px 30px black;border-radius:.5em;'>".$pinned."</div>";
