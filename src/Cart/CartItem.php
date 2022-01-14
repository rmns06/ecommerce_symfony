<?php

namespace App\Cart;

use App\Entity\Product;

class CartItem{
    public $qty;
    public $product;

    public function __construct(int $qty, Product $product) 
    {
        $this->qty = $qty;
        $this->product = $product;
    }

    public function GetTotal(): int
    {
        return ($this->product->getPrice() * $this->qty) ;
    }
}