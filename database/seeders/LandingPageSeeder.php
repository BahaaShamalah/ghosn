<?php



namespace Database\Seeders;



use App\Models\Page;

use App\Models\PageSection;

use App\Models\PageSectionBlock;

use Illuminate\Database\Seeder;



class LandingPageSeeder extends Seeder

{

    /** @var list<string> */

    private const PAGE_DATA_FILES = [

        'homepage.php',

        'volunteer.php',

        'who-we-are.php',

        'team.php',

        'contact.php',

    ];



    public function run(): void

    {

        foreach (self::PAGE_DATA_FILES as $file) {

            /** @var array{page: array<string, mixed>, sections: array<int, array<string, mixed>>} $data */

            $data = require database_path('data/'.$file);



            $this->seedPage($data);

        }

    }



    /**

     * @param  array{page: array<string, mixed>, sections: array<int, array<string, mixed>>}  $data

     */

    private function seedPage(array $data): void

    {

        $page = Page::query()->updateOrCreate(

            ['slug' => $data['page']['slug']],

            [

                'title_en' => $data['page']['title_en'],

                'title_ar' => $data['page']['title_ar'],

                'is_active' => $data['page']['is_active'],

                'meta_title_en' => $data['page']['meta_title_en'],

                'meta_title_ar' => $data['page']['meta_title_ar'],

                'meta_description_en' => $data['page']['meta_description_en'],

                'meta_description_ar' => $data['page']['meta_description_ar'],

            ],

        );



        foreach ($data['sections'] as $sectionData) {

            $section = PageSection::query()->firstOrNew([

                'page_id' => $page->id,

                'key' => $sectionData['key'],

            ]);

            $section->fill([

                'type' => $sectionData['type'],

                'title_en' => $sectionData['title_en'],

                'title_ar' => $sectionData['title_ar'],

                'sort_order' => $sectionData['sort_order'],

                'settings' => $sectionData['settings'] ?? ['source' => 'builder'],

            ]);

            if (! $section->exists) {

                $section->is_active = $sectionData['is_active'] ?? true;

            }

            $section->save();



            foreach ($sectionData['blocks'] as $blockData) {

                PageSectionBlock::query()->updateOrCreate(

                    [

                        'page_section_id' => $section->id,

                        'type' => $blockData['type'],

                        'sort_order' => $blockData['sort_order'],

                    ],

                    [

                        'content' => $blockData['content'],

                        'settings' => ['source' => 'seed'],

                        'is_active' => true,

                    ],

                );

            }

        }



        $validKeys = collect($data['sections'])->pluck('key')->all();



        $page->sections()

            ->whereNotIn('key', $validKeys)

            ->each(function (PageSection $section): void {

                $section->blocks()->delete();

                $section->delete();

            });

    }

}

