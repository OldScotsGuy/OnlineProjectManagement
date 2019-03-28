<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 11:20
 */

namespace Page;

use Utils\Action;
use Utils\PageName;
use Utils\Project;
use Utils\User;

class Page
{
    public $content = null;
    private $title = "Online Project Management Tool";
    private $keywords = "Gantt";
    private $buttons = array();

    function __construct() {
        $this->setNavigationLinks();
    }

    // Generalised setter
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    function setNavigationLinks() {
        if (isset($_SESSION[User::Role])) {
            $this->buttons = array( "Project Status"   => "index.php?page=". PageName::Status,
                "Project Docs" => "index.php?page=". PageName::Document ."&action=" . Action::View);

            if ($_SESSION[User::Role] == User::RoleAdmin || $_SESSION[User::Role] == User::RoleLead) {
                $this->buttons += array("User Entry"  => "index.php?page=". PageName::User,
                    "Project Entry" => "index.php?page=".PageName::Project,
                    "Task Entry" => "index.php?page=". PageName::Task);
            }
        }
    }

    public function Display()
    {
        echo "<!DOCTYPE html>\n<html lang=\"en\">\n";
        $this -> DisplayHead();
        $this -> DisplayBody();
        echo "</html>\n";
    }

    public function DisplayHead() {
        echo "<head>\n";
        echo "<meta charset=\"UTF-8\" name=\"keywords\" content=\"".$this->keywords."\"/>";
        echo "<title>".$this->title."</title>";
        ?>
        <link href="assets/CSS/page.css" type="text/css" rel="stylesheet">
        <link href="assets/CSS/chart-style.css" type="text/css" rel="stylesheet">
        <?php
        echo "</head>\n";
    }

    public function DisplayBody() {
        echo "<body>\n";
        $this -> DisplayHeader();
        $this -> DisplayMain();
        $this -> DisplayFooter();
        echo "</body>\n";
    }

    public function DisplayHeader()
    {
        ?>
        <!-- page header -->
        <header>
            <section>
                <img src="assets/images/#" alt="Logo Here" id="logo"/>
                <h1>Project Name Here</h1>
                <?php if (isset($_SESSION[User::Username])) echo "<p>Username:". $_SESSION[User::Username] ."</p>"?>
                <?php if (isset($_SESSION[User::Role])) echo "<p>Role:". ucfirst($_SESSION[User::Role]) ."</p>"?>
                <?php if (isset($_SESSION[User::Email])) echo "<p>Email:". ucfirst($_SESSION[User::Email]) ."</p>"?>
            </section>
            <nav>
                <ul>
                    <?php
                    reset($this->buttons);
                    foreach ($this->buttons as $name => $url) {
                        echo "<li><a href=\"".$url."\"><div><span>".$name."</span></div></a></li> ";
                    }
                    ?>
                </ul>
            </nav>
        </header>
        <?php
    }

    public function DisplayMain() {
        echo "<main>";
        echo $this->content;
        echo "</main>";
    }

    public function DisplayFooter()
    {
        ?>
        <!-- page footer -->
        <footer>
            <hr>
            <p><strong>(c)2016 Lex & Associates</strong></p>
            <script type="text/javascript" src="Assets/Javascript/chart-task-notes.js"></script>
        </footer>
        <?php
    }
}