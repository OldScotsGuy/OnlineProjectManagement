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
        "User Entry"  => "index.php?page=user&action=create",
        "Project Entry" => "index.php?page=project",
        "Task Entry" => "index.php?page=task",
        "Upload Docs" => "index.php?page=document"
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
        </footer>
        <?php
    }
}