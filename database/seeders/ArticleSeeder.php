<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleTranslation;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Article 1: public visibility with translations in English and Estonian
        $publicArticle = Article::firstOrCreate(
            ['article_id' => 1],
            [
                'node_type'  => 'article',
                'visibility' => 'public',
            ]
        );

        ArticleTranslation::firstOrCreate(
            ['article_id' => $publicArticle->article_id, 'language_code' => 'en'],
            [
                'title'       => 'Welcome to Our Blog',
                'path'        => 'welcome-to-our-blog',
                'summary'     => 'An introductory public article available to everyone.',
                'keywords'    => 'welcome, blog, introduction',
                'content'     => '<p>Welcome to our multilingual blog. This article is publicly accessible.</p>',
                'created_by'  => null,
                'modified_by' => null,
                'status'      => 'published',
            ]
        );

        ArticleTranslation::firstOrCreate(
            ['article_id' => $publicArticle->article_id, 'language_code' => 'et'],
            [
                'title'       => 'Tere tulemast meie blogisse',
                'path'        => 'tere-tulemast-meie-blogisse',
                'summary'     => 'Sissejuhatav avalik artikkel, mis on kõigile kättesaadav.',
                'keywords'    => 'tere tulemast, blogi, sissejuhatus',
                'content'     => '<p>Tere tulemast meie mitmekeelsesse blogisse. See artikkel on avalikult kättesaadav.</p>',
                'created_by'  => null,
                'modified_by' => null,
                'status'      => 'published',
            ]
        );

        // Article 2: private visibility with translations in English and Estonian
        $privateArticle = Article::firstOrCreate(
            ['article_id' => 2],
            [
                'node_type'  => 'article',
                'visibility' => 'private',
            ]
        );

        ArticleTranslation::firstOrCreate(
            ['article_id' => $privateArticle->article_id, 'language_code' => 'en'],
            [
                'title'       => 'Internal Guidelines',
                'path'        => 'internal-guidelines',
                'summary'     => 'A private article visible only to authenticated users.',
                'keywords'    => 'internal, guidelines, private',
                'content'     => '<p>This is a private article containing internal guidelines. API key required.</p>',
                'created_by'  => null,
                'modified_by' => null,
                'status'      => 'published',
            ]
        );

        ArticleTranslation::firstOrCreate(
            ['article_id' => $privateArticle->article_id, 'language_code' => 'et'],
            [
                'title'       => 'Sisemised juhised',
                'path'        => 'sisemised-juhised',
                'summary'     => 'Privaatne artikkel, mis on nähtav ainult autenditud kasutajatele.',
                'keywords'    => 'sisemised, juhised, privaatne',
                'content'     => '<p>See on privaatne artikkel sisemiste juhistega. Vajalik API võti.</p>',
                'created_by'  => null,
                'modified_by' => null,
                'status'      => 'published',
            ]
        );
    }
}
