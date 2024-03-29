<?php

namespace App\Http\Controllers;

use App\Enums\RecordState;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use Validator;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:question-edit')->only(['edit', 'update']);
        $this->middleware('permission:question-create')->only(['create', 'store']);
        $this->middleware('permission:question-delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Question::with(['category', 'answers'])->get();
        $result = [
            'data' => $data,
            'title' => __('admin.Questions'),
            'addUrl' => [
                'url' => route('questions.create'),
                'text' => __('admin.Add')
            ]
        ];
        return view('pages.questions.index', $result);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('record_state', RecordState::ACTIVE)->get();
        $selectedCategories = [];
        $result = [
            'title' => __('admin.Add New') . ' ' . __('Question'),
            'categories' => $categories,
            'selectedCategories' => $selectedCategories,
        ];
        return view('pages.questions.edit', $result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'category_id' => 'required',
            'answers' => 'required|array',
            'answers.*.answer' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', __('admin.Failed to create item'))->withErrors($validator)->withInput();
        }

        \DB::beginTransaction();
        $question = Question::create($request->only(['question', 'category_id', 'name']));
        if ($question) {
            $answers = [];
            foreach ($request->answers as $answer) {
                $answers[] = [
                    'answer' => $answer['answer'],
                    'is_correct' => $answer['is_correct'][0] ?? 0,
                ];
            }
            $answers = $question->answers()->createMany($answers);
            if ($answers) {
                if (isset($request->question_category)){
                    foreach ($request->question_category as $category){
                        QuestionCategory::create([
                            'question_id' => $question->id,
                            'category_id' => $category
                        ]);
                    }
                }
                \DB::commit();
                return redirect()->route('questions.index')->with('success', __('admin.Item created successfully'));
            }
            \DB::rollBack();
        }
        return redirect()->back()->with('error', __('admin.Failed to create item'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        $this->edit($id, $request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, Request $request)
    {
        $data = Question::with(['category', 'answers'])->findOrFail($id);
        $selectedCategories = QuestionCategory::where('question_id', $id)->pluck('category_id')->toArray();
        $result = [
            'selectedItem' => $data,
            'categories' => Category::where('record_state', RecordState::ACTIVE)->get(),
            'selectedCategories' => $selectedCategories,
            'title' => __('admin.Edit') . ' ' . __('admin.Question'),
        ];
        return view('pages.questions.edit', $result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'category_id' => 'required',
            'answers' => 'required|array',
            'answers.*.answer' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', __('admin.Failed to update item'))->withErrors($validator)->withInput();
        }

        \DB::beginTransaction();
        $question = Question::findOrFail($id);
        $question->update($request->only(['question', 'category_id', 'name']));
        if ($question) {
            $answers = [];
            foreach ($request->answers as $answer) {
                $answers[] = [
                    'answer' => $answer['answer'],
                    'is_correct' => $answer['is_correct'][0] ?? 0,
                ];
            }
            $answersDelete = $question->answers()->delete();
            if (!$answersDelete){
                \DB::rollBack();
                return redirect()->back()->with('error', __('admin.Failed to update item'));
            }
            $answers = $question->answers()->createMany($answers);
            if ($answers) {
                if (isset($request->question_category)){
                    QuestionCategory::where('question_id', $id)->delete();
                    foreach ($request->question_category as $category){
                        QuestionCategory::create([
                            'question_id' => $id,
                            'category_id' => $category
                        ]);
                    }
                }
                \DB::commit();
                return redirect()->route('questions.index')->with('success', __('admin.Item updated successfully'));
            }
            \DB::rollBack();
        }
        return redirect()->back()->with('error', __('admin.Failed to update item'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        $question = Question::findOrFail($id);
        $question->answers()->delete();
        $question->delete();
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('admin.Item deleted successfully')
            ]);
        }
        return redirect()->route('questions.index')->with('success', __('admin.Item deleted successfully'));
    }
}
