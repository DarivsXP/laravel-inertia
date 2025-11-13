<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Resources\ClassesResource;
use App\Models\Student;
use App\Models\Classes;
use App\Http\Controllers\UpdateStudentRequest;
use App\Http\Requests\UpdateStudentRequest as RequestsUpdateStudentRequest;
use Illuminate\Http\Request;
use App\Http\Resources\StudentResource;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $studentsQuery = Student::query();
        $this->applySearch($studentsQuery, $request->search);

        $students = StudentResource::collection($studentsQuery->paginate(10));

        return inertia('Students/Index', [
            'students' => $students,
        ]);
    }

    public function create()
    {
        $classes = ClassesResource::Collection(Classes::all());

        return inertia('Students/Create', [
            'classes' => $classes,
            'search' => $request->search ?? '',
        ]);
    }

    public function store(StoreStudentRequest $request){
        Student::create($request->validated());

        return redirect()->route('students.index');
    }

    public function edit(Student $student){
        $classes= ClassesResource::Collection(Classes::all());

        return inertia('Students/Edit', [
            'classes' => $classes,
            'student' => StudentResource::make($student),
        ]);
    }

    protected function applySearch($query, $search){
        return $query->when($search, function ($q) use ($search){
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%');
        });
    }

    public function update(RequestsUpdateStudentRequest $request, Student $student){

        $student->update($request->validated());
        return redirect()->route('students.index');
    }

    public function destroy(Student $student){
        $student->delete();

        return redirect()->route('students.index');
    }
}