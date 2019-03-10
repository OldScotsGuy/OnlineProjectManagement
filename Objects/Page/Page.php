<?php
/**
 * Created by PhpStorm.
 * User: nickh
 * Date: 10/03/2019
 * Time: 11:20
 */

namespace Page;

class Page
{
    public $content = null;
    public $title = "Online Project Management Tool";
    public $keywords = "Gantt";
    public $buttons = array("Project Status"   => "index.php?page=status",
        "User Entry"  => "index.php?page=entry",
        "Project Entry" => "index.php?page=project",
        "Task Entry" => "index.php?page=task",
        "Upload Docs" => "index.php?page=documents"
    );

    // Generalised setter
    public function __set($name, $value)
    {
        $this->$name = $value;
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
        <link rel="stylesheet" href="assets/CSS/unsemantic-grid-responsive-tablet.css">
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
        <header class="grid-container">
            <section class="grid-100">
                <img src="assets/images/cheese.png" alt="The Big Cheese" id="logo"/>
                <h1>The Magical World of Cheese!</h1>
            </section>
            <nav class="grid-100">
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
        echo "<main class=\"grid-container\">";
        echo $this->content;
        echo "</main>";
    }

    public function DisplayFooter()
    {
        ?>
        <!-- page footer -->
        <footer>
            <hr>
            <p><strong>(c)2016 The Mice People</strong></p>
        </footer>
        <?php
    }
}