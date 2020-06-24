<?php

trait PublicEndpoints
{
    // Function to display a page's content
    public function content($path)
    {
        $path = implode('/', $path);
        $result = $this->model->page->get(array('path' => $path));
        $page = $result->fetch_object();

        return FishFormat($page->Content);
    }

    // Function to display a page's source
    public function source($path)
    {
        $path = implode('/', $path);
        $result = $this->model->page->get(array('path' => $path));
        $page = $result->fetch_object();

        return $page->Content;
    }

    // Function to display JSON containing a page's content
    public function json($path)
    {
        $path = implode('/', $path);
        $page_result = $this->model->page->get(array('path' => $path));
        $page = $page_result->fetch_object();

        $tag_result = $this->model->tags->get(array('pageID' => $page->ID));
        $tags = array();
        
        while($tag = $tag_result->fetch_object())
        {
            $tags[] = str_replace('-', ' ', $tag->tag);
        }

        if(empty($page))
        {
            $response = array
            (
                'status' => 'error',
                'message' => 'Page does not exist.'
            );

            header('Content-Type: application/json');
            return json_encode($response);
        }
        else
        {
            $response = array
            (
                'status' => 'success',
                'path' => $page->Path,
                'views' => $page->Views,
                'edits' => count(explode(',', $page->Edits)),
                'modified' => $page->EditTime,

                'title' => array
                (
                    'formatted' => FishFormat($page->Title),
                    'source' => $page->Title
                ),

                'content' => array
                (
                    'formatted' => FishFormat($page->Content),
                    'source' => $page->Content
                ),

                'tags' => $tags
            );

            header('Content-Type: application/json');
            return json_encode($response);
        }
    }

    // Function to return recent edits
    public function recent($path)
    {
        $ActivityQuery = "
        SELECT `ID`,`PageID`,`AccountID`,`EditTime`,`Size`,`Tags`,`TagList`, `Name`,`Description`,`Title` 
        FROM `Wiki_Edits` 
        WHERE `Archived` = 0 
        ORDER BY `ID` DESC LIMIT 50";

        $query = mysql_query($ActivityQuery);
        $output = array();
        while ($row = mysql_fetch_assoc($query))
        {
            $output[] = $row;
        }

        return json_encode($output);
    }
}
