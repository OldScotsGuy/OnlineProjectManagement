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

    function Display()
    {
        echo "<!DOCTYPE html>\n<html lang=\"en\">\n";
        $this -> DisplayHead();
        $this -> DisplayBody();
        echo "</html>\n";
    }

    function DisplayHead() {
        echo "<head>\n";
        echo "<meta charset=\"UTF-8\" name=\"keywords\" content=\"".$this->keywords."\"/>";
        echo "<title>".$this->title."</title>";
        ?>
        <link href="assets/CSS/page.css" type="text/css" rel="stylesheet">
        <link href="assets/CSS/chart-style.css" type="text/css" rel="stylesheet">
        <?php
        echo "</head>\n";
    }

    function DisplayBody() {
        echo "<body>\n";
        $this -> DisplayHeader();
        $this -> DisplayMain();
        $this -> DisplayFooter();
        echo "</body>\n";
    }

    function DisplayHeader()
    {
        ?>
        <!-- page header -->
        <header>
            <section>
                <img src="Assets/Images/Road-Ahead-Small.jpg" alt="Road Ahead" id="logo"/>
                <div>
                <h1>Plan the Next Move of Your Journey</h1>
                <?php if (isset($_SESSION[User::Email])) echo "<span><a href='index.php?action=". Action::Logout ."'>Logout</a></span>"?>
                <?php //if (isset($_SESSION[User::Role])) echo "<span>Role:   ". ucfirst($_SESSION[User::Role]) ."</span>"?>
                <?php if (isset($_SESSION[User::Username])) echo "<span>Logged in As:   ". $_SESSION[User::Username] ."</span>"?>
                </div>
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

    function DisplayMain() {
        echo "<main>";
        echo '<section>' . $this->content . '</section>';
        echo "</main>";
    }

    function DisplayFooter()
    {
        ?>
        <!-- page footer -->
        <footer>
            <hr>
            <p><strong>(c) 2016 Lex & Associates</strong></p>
            <script type="text/javascript" src="Assets/Javascript/chart-task-notes.js"></script>
        </footer>
        <?php
    }
}