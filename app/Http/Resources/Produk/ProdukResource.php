<?php

namespace App\Http\Resources\Produk;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id ?? null,
            "woo_produk_id" => $this->woo_produk_id,
            "m_kategori_id" => $this->m_kategori_id,
            "m_brand_id" => $this->m_brand_id,
            "sku" => $this->sku,
            "nama" => $this->nama,
            "slug" => $this->slug,
            "type" => $this->type,
            "harga" => $this->harga,
            "berat" => $this->berat,
            "panjang" => $this->panjang,
            "tinggi" => $this->tinggi,
            "lebar" => $this->lebar,
            "link_tokped" => $this->link_tokped,
            "link_shopee" => $this->link_shopee,
            "link_bukalapak" => $this->link_bukalapak,
            "link_lazada" => $this->link_lazada,
            "link_blibli" => $this->link_blibli,
            "detail_produk" => $this->detail_produk,
            "deskripsi" => $this->deskripsi,
            "in_box" => $this->in_box,
            "min_beli" => $this->min_beli,
            "link_video" => $this->link_video,
            "stok" => $this->stok,
            "stok_status" => $this->stok_status,
            "is_active" => $this->is_active,
            "created_at" => $this->created_at,
            "created_by" => $this->created_by,
            "updated_at" => $this->updated_at,
            "updated_by" => $this->updated_by,
            "kategori" => $this->kategori,
            "slug_kategori" => $this->slug_kategori,
            "brand" => $this->brand,
            "slug_brand" => $this->slug_brand,
            "photo_product" => [],
            "foto" => "https://jakartacamera.com/wp-content/uploads/2024/10/3.jpg",
            "video" => null,
            "categories" => [],
            "tags" => [],
        ];
    }
}
