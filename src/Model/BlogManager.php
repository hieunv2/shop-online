<?php

class BlogManager extends BaseModal
{

    /**
     * @var mysqli
     */
    private $db;

    private $name;


    public function __construct($dbConnection, $name)
    {
        $this->$name = $name;
        if ($dbConnection instanceof mysqli) {
            $this->db = $dbConnection;
        } else {
            throw new Exception('Connection injected should be of Mysqli object');
        }
    }


    /**
     * Get all posts with status 'published' from the database
     * @return array
     */
    public function findAllProducts($start)
    {
        $limit=20;
        $products = [];
        $query = "SELECT p.*,c.name as 'cat_name' FROM product p JOIN category c ON p.category_id = c.id  ORDER BY id ASC LIMIT $start,$limit";
        $result = $this->db->query($query);
        if ($result) {
            // Cycle through results
            while ($row = $result->fetch_assoc()) {
                $products[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'image' => $row['image'],
                    'price' => $row['price'],
                    'note' => $row['note'],
                    'sale_price' => $row['sale_price']
                ];
            }
            // Free result set
            $result->close();
        } else {
            echo($this->db->error);
        }
        return $products;
    }

    public function findAllProductsByCategory($id,$name)
    {
        $products = [];
      if($id){
          $query = ""
              . "SELECT * "
              . "FROM product "
              . "WHERE category_id = '$id' AND name LIKE '%$name%'";
      } else {
          $query = ""
              . "SELECT * "
              . "FROM product "
              . "WHERE name LIKE '%$name%'";
      }
        $result = $this->db->query($query);
        if ($result) {
            // Cycle through results
            while ($row = $result->fetch_assoc()) {
                $products[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'image' => $row['image'],
                    'price' => $row['price'],
                    'note' => $row['note'],
                    'sale_price' => $row['sale_price']
                ];
            }
            // Free result set
            $result->close();
        } else {
            echo($this->db->error);
        }
        return $products;
    }


    /**
     * Get one post by it's ID
     * @param Int $id
     * @return array
     */
    public function findOnePostById($id)
    {
        $query = ""
            . "SELECT * "
            . "FROM product "
            . "WHERE id = '%s'";
        $query = sprintf($query, $this->db->real_escape_string($id));
        if ($result = $this->db->query($query)) {
            $row = $result->fetch_assoc();
            $post = [
                'id' => $row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'image' => $row['image'],
                'price' => $row['price'],
                'note' => $row['note'],
                'sale_price' => $row['sale_price']
            ];
            $result->close();
        } else {
            die($this->db->error);
        }
        return $post;
    }

    public function findProductBanner()
    {
        $query = ""
            . "SELECT * "
            . "FROM product "
            . "WHERE image like 'banner_product%'";
        if ($result = $this->db->query($query)) {
            $row = $result->fetch_assoc();
            $post = [
                'id' => $row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'image' => $row['image'],
                'price' => $row['price'],
                'note' => $row['note'],
                'sale_price' => $row['sale_price']
            ];
            $result->close();
        } else {
            die($this->db->error);
        }
        return $post;
    }



    public function findAllCommentsByPostId($id)
    {
        $comments = [];
        $query = ""
            . "SELECT * "
            . "FROM comment "
            . "WHERE post_id = '%d'";
        $query = \sprintf($query, $this->db->real_escape_string($id));
        $result = $this->db->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $comments[] = [
                    'id' => $row['id'],
                    'post_id' => $row['post_id'],
                    'content' => $row['content'],
                    'author' => $row['author'],
                    'author_url' => $row['author_url'],
                    'author_email' => $row['author_email'],
                    'date_created' => $row['date_created']
                ];
            }
            $result->close();
        } else
            die($this->db->error);
        return $comments;

    }


    public function addPost($title, $content, $userid)
    {
        $query = "INSERT INTO post (
              `title`, `content`, `status`, `id_user`, `date_created`
          )
          VALUES (
              '%s', '%s', 2, '%d', NOW()
          )";
        $query = \sprintf($query, $this->db->real_escape_string($title), $this->db->real_escape_string($content), $this->db->real_escape_string($userid));
        if ($result = $this->db->query($query)) {
            return true;
        } else {
            die($this->db->error);
        }
    }
}
