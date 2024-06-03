<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class News extends Model
{
    use HasFactory;

    protected $table = 'm_news';

    protected $fillable = [
        'id',
        'm_news_kategori_id',
        'judul',
        'slug',
        'image',
        'image_alt',
        'content',
        'meta_content',
        'tags',
        'views',
        'is_publish',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'publish_at',
        'publish_by'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params){
        $query = DB::table($this->table)
        ->select(
            'm_news.*',
            'm_news_kategori.kategori',
            'a.name as pembuat',
            'b.name as pengubah',
            'p.name as publish',
        )
        ->leftJoin('m_news_kategori','m_news_kategori.id', '=', 'm_news.m_news_kategori_id')
        ->leftJoin('users as a','a.id', '=', 'm_news.created_by')
        ->leftJoin('users as b','b.id', '=', 'm_news.updated_by')
        ->leftJoin('users as p','p.id', '=', 'm_news.publish_by');

        if (isset($params['filter']) && !empty($params['filter'])) {
            $filter = json_decode($params['filter']);
            foreach ($filter as $key => $value) {
                if($key === 'judul' && !empty($value)){
                    $query->where('judul', 'like', '%' . $value . '%');
                }
                if($key === 'kategori' && !empty($value)){
                    $query->where('m_news_kategori.kategori', '=', $value);
                }
            }
        }

        if (isset($params['judul']) && !empty($params['judul'])) {
            $query->where('judul', 'like', '%' . $value . '%');
        }

        if (isset($params['kategori']) && !empty($params['kategori'])) {
            $query->where('m_news_kategori.kategori', '=', $params['kategori']);
        }

        if (isset($params['notEqual']) && !empty($params['notEqual'])) {
            $query->where("id", "!=", $params['notEqual']);
        }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $data = $query->orderBy('created_at', 'DESC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->tags = explode(",", $value->tags);
            $data[$key]->image = Storage::url('images/news/' . $value->image);
        }
        $totalItems = $query->count();
        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getBySlug($slug){
        $query = DB::table($this->table)
        ->select(
            'm_news.*',
            'm_news_kategori.kategori',
            'a.name as pembuat',
            'b.name as pengubah',
            'p.name as publish',
        )
        ->where('slug', '=', $slug)
        ->leftJoin('m_news_kategori','m_news_kategori.id', '=', 'm_news.m_news_kategori_id')
        ->leftJoin('users as a','a.id', '=', 'm_news.created_by')
        ->leftJoin('users as b','b.id', '=', 'm_news.updated_by')
        ->leftJoin('users as p','p.id', '=', 'm_news.publish_by');

        $data = $query->first();

        $data->tags = explode(",", $data->tags);
        $data->image = Storage::url('images/news/' . $data->image);

        return [
            'article' => $data
        ];
    }

    public function getById($id){
        $data = DB::table($this->table)
            ->where('id', $id)
            ->first();

        return $data;
    }

    public function changeStatus($params)
    {
        $id = $params['id'];
        $params['updated_at'] = date('Y-m-d H:i:s');
        $params['updated_by'] = Auth::user()->id;
        $data = DB::table($this->table)->where('id', $id)->update($params);

        return $data;
    }

    public function simpan($params) {
        $modelKategori = new NewsKategori();
        $params['tags'] = implode(", ", $params['tags']);
        if(is_string($params['m_news_kategori_id'])){
            $params['m_news_kategori_id'] = $modelKategori->simpan($params['m_news_kategori_id']);
        }

        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateNews($params);
        } else {
            return $this->insertNews($params);
        }
    }

    public function updateNews($params) {
        $service = new Service();

        $id = $params['id']; unset($params['id']);
        $params['slug'] = Str::slug($params['judul'], '-');
        $params['updated_at'] = date('Y-m-d H:i:s');
        $params['updated_by'] = Auth::user()->id;
        $params['image'] = $service->saveImage("news/", $params['image']);

        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function insertNews($params) {
        $service = new Service();

        $params['id'] = Generator::uuid4()->toString();
        $params['slug'] = Str::slug($params['judul'], '-');
        $params['created_at'] = date('Y-m-d H:i:s');
        $params['created_by'] = Auth::user()->id;
        $params['image'] = $service->saveImage("news/", $params['image']);

        return DB::table($this->table)->insert($params);
    }

    public function getNews(){
        $data = DB::table($this->table)->orderBy('created_at', 'DESC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->image = Storage::url('images/news/' . $value->image);
        }

        return $data;
    }
}
