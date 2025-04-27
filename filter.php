<?php
class ProductFilter
{
    private $pdo;
    private $search;
    private $min_price;
    private $max_price;
    private $category_id;
    private $size_id;
    private $result;
    private $categories;
    private $sizes;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function applyFilters($params)
    {
        $this->search = isset($params['query']) ? trim($params['query']) : '';
        $this->min_price = isset($params['min_price']) && $params['min_price'] !== '' ? (float)$params['min_price'] : null;
        $this->max_price = isset($params['max_price']) && $params['max_price'] !== '' ? (float)$params['max_price'] : null;
        $this->category_id = isset($params['category']) && $params['category'] !== '' ? (int)$params['category'] : null;
        $this->size_id = isset($params['size']) && $params['size'] !== '' ? (int)$params['size'] : null;

        $this->buildQuery();
        $this->loadCategories();
        $this->loadSizes();
    }

    private function buildQuery()
    {
        try {
            $sql = "SELECT p.* FROM products p 
                    LEFT JOIN product_categories pc ON p.id = pc.product_id 
                    LEFT JOIN product_sizes ps ON p.id = ps.product_id 
                    WHERE p.name LIKE :search";

            if ($this->min_price !== null && $this->max_price !== null) {
                $sql .= " AND p.price BETWEEN :min_price AND :max_price";
            } elseif ($this->min_price !== null) {
                $sql .= " AND p.price >= :min_price";
            } elseif ($this->max_price !== null) {
                $sql .= " AND p.price <= :max_price";
            }

            if ($this->category_id !== null) {
                $sql .= " AND pc.category_id = :category_id";
            }

            if ($this->size_id !== null) {
                $sql .= " AND ps.size_id = :size_id";
            } else {
                $sql .= " AND (ps.size_id IS NULL OR ps.size_id != 0)";
            }

            if ($this->min_price === null && $this->max_price === null) {
                $sql .= " ORDER BY p.price ASC";
            }

            $stmt = $this->pdo->prepare($sql);
            $searchTerm = "%" . $this->search . "%";
            $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);

            if ($this->min_price !== null && $this->max_price !== null) {
                $stmt->bindParam(':min_price', $this->min_price);
                $stmt->bindParam(':max_price', $this->max_price);
            } elseif ($this->min_price !== null) {
                $stmt->bindParam(':min_price', $this->min_price);
            } elseif ($this->max_price !== null) {
                $stmt->bindParam(':max_price', $this->max_price);
            }

            if ($this->category_id !== null) {
                $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
            }

            if ($this->size_id !== null) {
                $stmt->bindParam(':size_id', $this->size_id, PDO::PARAM_INT);
            }

            $stmt->execute();
            $this->result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    private function loadCategories()
    {
        $stmt = $this->pdo->query("SELECT * FROM categories");
        $this->categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function loadSizes()
    {
        if ($this->category_id !== null) {
            $stmt = $this->pdo->prepare("SELECT s.id, s.size_label 
                                         FROM sizes s 
                                         JOIN categories c ON s.category_id = c.id 
                                         WHERE c.id = :category_id");
            $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
            $stmt->execute();
            $this->sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $this->sizes = [];
        }
    }

    // Getters
    public function getResults()
    {
        return $this->result;
    }
    public function getCategories()
    {
        return $this->categories;
    }
    public function getSizes()
    {
        return $this->sizes;
    }
    public function getCurrentCategory()
    {
        return $this->category_id;
    }
    public function getCurrentSize()
    {
        return $this->size_id;
    }
    public function getSearchTerm()
    {
        return $this->search;
    }
    public function getMinPrice()
    {
        return $this->min_price;
    }
    public function getMaxPrice()
    {
        return $this->max_price;
    }
}
