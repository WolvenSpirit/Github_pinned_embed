<style>
.show{
  display: grid;
}
.hide{
  display: none;
}
</style>
<script type="application/javascript">
function toggleRepositories()
{
  var pinned = document.getElementById("pinned");
  var allrepo = document.getElementById("allrepo");
  if(pinned.classList.contains("show"))
  {
    pinned.classList.remove("show");
    pinned.classList.add("hide");
    allrepo.classList.remove("hide");
    allrepo.classList.add("show");
  }
  else
  {
    allrepo.classList.remove("show");
    allrepo.classList.add("hide");
    pinned.classList.remove("hide");
    pinned.classList.add("show");
  }
}
</script>
<div class="container"
style='color:darkslategrey;padding:.5em;box-shadow:inset 0px 0px 90px 10px grey;border-radius:.5em;overflow:auto;height:30em;font-size:1em;'>
<button style="position:sticky;top:0;box-shadow: 0px 0px 50px 5px grey;" onclick="toggleRepositories()">Repositories</button>
<?php
include "github_user_repo.php";

$user_page = $user->fetch_profile();
$pinned_repositories = $user->find_pinned_repositories($user_page);
# Needs preg_replace to prepend the base_url to the links.
$pinned = preg_replace("/href=\"/", "/target=blank href=\"https://github.com/", $pinned_repositories[0]);
# Modify the echo'd style or give the div an id and modify from your global css.
echo "<div id='pinned' class='show'>".$pinned."</div>";

$all_repositories = $user->fetch_all();
$all = preg_replace("/href=\"/", "/target=blank href=\"https://github.com/", $all_repositories[0]);
echo "<div id='allrepo' class='hide'>
<h1>All repositories</h1>".$all."</div>";
?>
</div>
