<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloBuilder;
use Illuminate\Database\Query\Builder as QueBuilder;
use Illuminate\Support\Facades\DB;


class Post extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $fillable = [
        'title',
        'content',
        'image',
        'likes',
        'publish_date',
        'is_published',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class,'post_tags','post_id','tag_id');
    }

    private static function bindValueToSQL(EloBuilder|QueBuilder $builder)
    {
        $sql = $builder->toSql();
        foreach ($builder->getBindings() as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        return $sql;
    }

    public function findByRating($period)
    {

        $posts = $this::select('user_id', 'publish_date')
            ->selectRaw('IFNULL(IF(TIMESTAMPDIFF(hour, publish_date, LAG(publish_date, 1)
                  OVER (PARTITION BY user_id ORDER BY publish_date DESC))>?,0,1),0) AS delta ', [$period]);

        $subA1 = DB::table(DB::raw("({$this::bindValueToSQL($posts)}) a1"))
            ->select('a1.user_id', 'a1.publish_date', 'a1.delta')
            ->selectRaw('IFNULL(LAG(a1.delta) OVER (PARTITION BY a1.user_id ORDER BY a1.publish_date),0) AS prev_delta');

        $subA2 = DB::table(DB::raw("({$subA1->toSql()}) a2"))
            ->select('a2.user_id', 'a2.publish_date', 'a2.delta', 'a2.prev_delta')
            ->selectRaw('SUM(CASE WHEN a2.delta <> a2.prev_delta THEN 1 ELSE 0 END) OVER
                        (PARTITION BY a2.user_id ORDER BY a2.publish_date) AS `grouping`')
            ->where('a2.delta', '=', 1);

        $subA3 = DB::table(DB::raw("({$this::bindValueToSQL($subA2)}) a3"))
            ->distinct()
            ->select('a3.user_id')
            ->selectRaw('COUNT(a3.`grouping`) OVER (PARTITION BY a3.user_id, a3.`grouping`
                            ORDER BY a3.publish_date ROWS BETWEEN UNBOUNDED PRECEDING
                            AND UNBOUNDED FOLLOWING ) as max_group_count');

        $subA4 = DB::table(DB::raw("({$subA3->toSql()}) a4"))
            ->select('a4.user_id', 'a4.max_group_count')
            ->selectRaw('max(a4.max_group_count) OVER (PARTITION BY a4.user_id) as max_streak');

        $subA5 = DB::table(DB::raw("({$subA4->toSql()}) a5"))
            ->join('users', 'users.id', '=', 'a5.user_id')
            ->select('a5.user_id', 'a5.max_streak', 'users.name')
            ->whereColumn('a5.max_group_count', '=', 'a5.max_streak')
            ->orderByDesc('a5.max_streak');

        return $subA5;
    }

    public function findByTagNames($names)
    {

        $tag_names = explode(',', $names);
        $tagIds = [];

        $tags = Tag::whereIn('tag', $tag_names)->get();
        foreach ($tags as $tag) {
            $tagIds[] = $tag->id;
        }

        $posts = Post::where(function ($query) use ($tagIds) {
            foreach ($tagIds as $value) {
                $query->whereHas('tags', function ($query) use ($value) {
                    $query->where('tag_id', $value);
                });
            }
        });

        $posts = $posts->with('user')->with('tags');

        return $posts;
    }

    public function findByTagIds($ids)
    {

        $tagIds = explode(',', $ids);
        $tagIdsFiltered = [];

        $tags = Tag::whereIn('id', $tagIds)->get();
        foreach ($tags as $tag) {
            $tagIdsFiltered[] = $tag->id;
        }

        $posts = Post::where(function ($query) use ($tagIdsFiltered) {
            foreach ($tagIdsFiltered as $value) {
                $query->whereHas('tags', function ($query) use ($value) {
                    $query->where('tag_id', $value);
                });
            }
        });
        $posts = $posts->with('user')->with('tags');

        return $posts;
    }
}
