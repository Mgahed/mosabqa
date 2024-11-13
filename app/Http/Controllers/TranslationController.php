<?php

namespace App\Http\Controllers;

use App\Http\Resources\TranslationResource;
use App\Jobs\TranslateJob;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Finder;

class TranslationController extends Controller
{

    public function __construct()
    {

    }
    public function jsonTranslation(Request $request)
    {

        $parent = $request->parent == "#" ? 0 : $request->parent;

        $this->calculateChild($parent);

        $data = Translation::where('parent_id', $parent)
            ->where('site_lang', 'en')
            ->where('child_count', '<>', 0)
            ->get();

        $result = TranslationResource::collection($data);

        return response()->json($result);
    }

    function calculateChild($parent)
    {

        $data = Translation::where('parent_id', $parent)->get();

        foreach ($data as $item) {
            $item->child_count = Translation::where('parent_id', $item->id)->get()->count();
            $item->save();
        }
    }

    public function index(Request $request)
    {

        $result = [];

        $data = Translation::whereNull('parent_id');

        $result['data'] = $data->get();

        $result['Site'] = sites();

        $result['filter'] = $request;

        session(['link' => url()->previous()]);

        $result['unpublishedItems'] = $data = Translation::where(function ($query) {
            $query->whereNull('translation_published')->orWhere('translation_published', 0);
        })
            ->whereNotNull('translations')
            ->whereNotNull('full_path')
            ->get()->count();

        return view('admin.system.translation.index')->with($result);
    }

    public function show($id)
    {

        $result = [];

        $result['data'] = $data = Translation::where('parent_id', $id)->get();

        if (count($data) == 0) {
            return redirect()->back()->with('error', __('admin.Record not found'));
        }

        $result['Sites'] = sites();

        \Log::critical('TranslationController.show', ['result' => $result]);
        return view('admin.system.translation.item')->with($result);
    }

    public function update(Request $request)
    {

        $selectedItem = Translation::find($request->id);

        $translations = json_decode($selectedItem->translations, true);
        $translations[$request->lang] = $request->text;

        $selectedItem->translation_published = 0;
        $selectedItem->translations_changed = 1;
        $selectedItem->translations = json_encode($translations);
        $selectedItem->translations_raw = serialize($translations);
        $selectedItem->save();
    }

    public function updateGlobal(Request $request)
    {
        foreach (Translation::where('translation_key', $request->id)->get() as $selectedItem) {
            $translations = json_decode($selectedItem->translations, true);
            $translations[$request->lang] = $request->text;

            $selectedItem->translation_published = 0;
            $selectedItem->translations_changed = 1;
            $selectedItem->translations = json_encode($translations);
            $selectedItem->translations_raw = serialize($translations);
            $selectedItem->save();
        }
    }

    public function addCustomKey(Request $request)
    {

        $customKey = $request->customKey;

        $destinationPath = storage_path('app/public/customKey');
        $fPath = $destinationPath . '/keys.php';
        // @codeCoverageIgnoreStart
        if (!File::exists($destinationPath)) {
            Storage::makeDirectory($destinationPath, 0777);
        }
        // @codeCoverageIgnoreEnd
        if (explode('.', $customKey)[0] != 'custom') {
            $customKey = 'custom' . '.' . $customKey;
        }

        file_put_contents($fPath, '__' . '(' . "'" . $customKey . "'" . ');' . PHP_EOL, FILE_APPEND);

        return redirect()->route('translations.index')->with('success', __('admin.system.translations.New key added successfully'));

    }

    public function search(Request $request)
    {

        $result = [];

        $data = Translation::whereNotNull('full_path')->whereNotNull('translations');

        $find_in_value = $request->find_in_value;
        if ($find_in_value != null) {

            switch ($request->find_in_operator) {
                case 'contain':
                    $data = $data->where($request->find_in_field, 'like', '%' . $find_in_value . '%');
                    break;
                case 'equal_to':
                    $data = $data->where($request->find_in_field, $find_in_value);
                    break;
                case 'start_with':
                    $data = $data->where($request->find_in_field, 'like', $find_in_value . '%');
                    break;
                case 'end_with':
                    $data = $data->where($request->find_in_field, 'like', '%' . $find_in_value);
                    break;
            }
        }

        if ($request->translation_published != null) {

            $data = $data->where('translation_published', $request->translation_published);
        }

        if ($request->site_id != null) {
            $data = $data->where('site_id', $request->site_id);
        }

        $result['data'] = $data->get();
        $result['Sites'] = sites();

        return view('admin.system.translation.item')->with($result);
    }

    public function output($parentId, $siteLang)
    {

        $data = Translation::where('parent_id', $parentId)
            ->get();

        $string = "[" . PHP_EOL;
        foreach ($data as $i => $project) {
            $string .= '"' . $project['translation_key'] . '" => ';
            if ($project['full_path'] === null) {
                $string .= $this->output($project->id, $siteLang);
            } else {
                $string .= '"' . @json_decode($project->translations, true)[$siteLang] . '",' . PHP_EOL;
            }
        }
        $string .= "]," . PHP_EOL;

        return $string;
    }

    public function publishTranslation()
    {

        $data = Translation::whereNull('full_path')
            ->where('parent_id', 0)
            ->get();

        $sites = sites();

        foreach ($data as $item) {
            foreach ($sites as $Site) {
                // if resources/lang/ folder not exist create it
                if (env("APP_ENV") == "testing" || env("APP_ENV") == "local") {
                    if (!File::exists(base_path('resources/lang/' . $Site['lang'] . '/'))) {
                        File::makeDirectory(base_path('resources/lang/' . $Site['lang'] . '/'), 0777, true);
                    }
                }
                $fPath = base_path('resources/lang/' . $Site['lang'] . '/' . $item->translation_key . '.php');

                $contents = $this->output($item->id, $Site['lang']);
                $contents = substr($contents, 0, -3) . '];';
                $contents = '<?php return ' . str_replace(']];', '];', $contents);
                // @codeCoverageIgnoreStart
                if (env("APP_ENV") != "testing") {
                    // replace $ with empty string
                    $contents = str_replace('$', '', $contents);
                    // replace -> with _
                    $contents = str_replace('->', '_', $contents);
                    File::put($fPath, $contents);
                }
                // @codeCoverageIgnoreEnd
            }
        }

        Translation::where(function ($query) {
            $query->whereNull('translation_published')->orWhere('translation_published', 0);
        })
            ->update(['translation_published' => 1]);

        return redirect()->route('translations.index')->with('success', __('admin.system.translations.Published'));
    }

    public function scanTranslation()
    {

        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $this->doFindTranslation();

        return redirect()->route('translations.index')->with('success', __('admin.system.translations.Scanned'));
    }

    public function unpublishedTranslation()
    {

        $result = [];

        $result['data'] = $data = Translation::where(function ($query) {
            $query->whereNull('translation_published')->orWhere('translation_published', 0);
        })
            ->whereNotNull('translations')
            ->whereNotNull('full_path')
            ->get();

        $result['Sites'] = sites();

        return view('admin.system.translation.item')->with($result);
    }

    public function doFindTranslation()
    {

        //Path to Views , Controllers , Modules , Resources , Rules
        $this->findTranslation(resource_path('views'));
        $this->findTranslation(app_path('Http/Controllers'));
        $this->findTranslation(app_path('Models'));
        $this->findTranslation(app_path('Http/Resources'));
        $this->findTranslation(app_path('Rules'));
        $this->findTranslation(app_path('Jobs'));
        $this->findTranslation(app_path('Helpers'));
        $this->findTranslation(app_path('Livewire'));

        $data = Translation::whereNotNull('full_path')->get();
        $sites = sites();

        foreach ($data as $item) {
            foreach ($sites as $site) {
                if ($item->translations_changed != 1) {
                    if (__($item->full_path, [], $site['lang']) == $item->full_path) {
                        $translations[$site['lang']] = str_replace('$', '', $item->translation_key);
                    } else {
                        $translations[$site['lang']] = __($item->full_path, [], $site['lang']);
                    }

                    $item->translations = json_encode($translations);
                    $item->translations_raw = serialize($translations);

                    $item->save();
                }
            }
        }
    }

    function findTranslation($path = null)
    {
//        if (str_contains($path, 'vendor/jawahr/')) {
//            $path = $path ?? resource_path('views');
//            dd($path);
//        }

        $path = $path ?? resource_path('views'); //if null just parse views
        $groupKeys = [];
        $functions = [
            'trans',
            'trans_choice',
            'Lang::get',
            'Lang::choice',
            'Lang::trans',
            'Lang::transChoice',
            '@lang',
            '@choice',
            '__',
        ];

        $groupPattern = "[^\w|>]" . "(" . implode('|', $functions) . ")" . "\(" . "[\'\"]" . "(" . "[a-zA-Z0-9_-]+" . "([.|\/][^\1)]+)+" . ")" . "[\'\"]" . "[\),]"; // Close parentheses or new parameter
        $finder = new Finder();
        $finder->in($path)->exclude('storage')->name('*.php')
            //			->name('*.twig')
            //			->name('*.vue')
            ->files();

        foreach ($finder as $file) {
            if (preg_match_all("/$groupPattern/siU", $file->getContents(), $matches)) {
                foreach ($matches[2] as $key) {
                    $groupKeys[] = trim($key);
                }
            }
        }

        $groupKeys = $this->array_iunique($groupKeys);

        $groupKeys = array_combine(array_values($groupKeys), array_values($groupKeys));


        $groupKeys = ($this->explodeTree($groupKeys, '.'));

        $this->buildTreeFromArray($groupKeys);
    }

    function array_iunique($array)
    {
        return array_intersect_key(
            $array,
            array_unique(array_map("strtolower", $array))
        );
    }

    function explodeTree($array, $delimiter = '_', $baseval = false)
    {
        if (!is_array($array)) return false;
        $splitRE = '/' . preg_quote($delimiter, '/') . '/';
        $returnArr = [];
        foreach ($array as $key => $val) {
            // Get parent parts and the current leaf
            $parts = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
            $leafPart = array_pop($parts);

            // Build parent structure
            // Might be slow for really deep and large structures
            $parentArr = &$returnArr;
            // @codeCoverageIgnoreStart
            foreach ($parts as $part) {
                if (!isset($parentArr[$part])) {
                    $parentArr[$part] = [];
                } elseif (!is_array($parentArr[$part])) {
                    if ($baseval) {
                        $parentArr[$part] = ['__base_val' => $parentArr[$part]];
                    } else {
                        $parentArr[$part] = [];
                    }
                }
                $parentArr = &$parentArr[$part];
            }

            // Add the final part to the structure
            if (empty($parentArr[$leafPart])) {
                $parentArr[$leafPart] = $val;
            } elseif ($baseval && is_array($parentArr[$leafPart])) {
                $parentArr[$leafPart]['__base_val'] = $val;
            }
            // @codeCoverageIgnoreEnd
        }
        return $returnArr;
    }

    function buildTreeFromArray($items, $parent = 0)
    {

        foreach ($items as $i => &$item) {

            if (is_array($item)) {
                $data = Translation::firstOrCreate([
                    'parent_id' => $parent,
                    'translation_key' => $i,
                ]);

                $this->buildTreeFromArray($item, $data->id);
            } else {
                $data = Translation::firstOrCreate([
                    'full_path' => $item,
                    'parent_id' => $parent,
                    'translation_key' => $i,
                ]);
            }
        }
    }

    // @codeCoverageIgnoreStart
    function initiate($cod = null)
    {

        //use it 1st Time only if necessary
        if ($cod != '123123') {
            return false;
        }

        //just SuperAdmin Can Do This
        if (auth()->user()->role_id != 1) {
            return false;
        }

        //TRUNCATE Table;
        DB::update('TRUNCATE translations;');

        //Find Translation
        $this->doFindTranslation();

        //Publish Translation
//		$this->publishTranslation();
    }

    public function translateAll($langCode = 'ar')
    {
        dispatch(new TranslateJob($langCode))->delay(now()->addSeconds(5));
        return redirect()->route('translations.index')->with('success', __('admin.system.translations.Translated'));
    }
}
