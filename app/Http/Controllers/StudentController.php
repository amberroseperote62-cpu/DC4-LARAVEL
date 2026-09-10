<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // GET /api/students — return all students
    // Bonus: supports ?course=BSIT filtering and pagination (10 per page)
    public function index(Request $request)
    {
        $query = Student::query();

        // Bonus: search/filter by course, e.g. GET /api/students?course=BSIT
        if ($request->has('course')) {
            $query->where('course', $request->query('course'));
        }

        // Bonus: paginate results, 10 students per page
        $students = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $students,
        ], 200);
    }

    // POST /api/students — create a new student
    public function store(Request $request)
    {
        // Bonus: validation — rejects requests with missing required fields
        $validated = $request->validate([
            'student_no' => 'required|string|max:20|unique:students,student_no',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'course'     => 'required|string|max:50',
            'year_level' => 'required|integer|min:1|max:4',
            'email'      => 'nullable|email|max:150',
        ]);

        $student = Student::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully.',
            'data' => $student,
        ], 201);
    }

    // GET /api/students/{id} — return one student
    public function show(string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }

        return response()->json(['success' => true, 'data' => $student], 200);
    }

    // PUT /api/students/{id} — update a student
    public function update(Request $request, string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }

        // Bonus: validation — 'sometimes' means only validate fields that are actually sent
        $validated = $request->validate([
            'student_no' => 'sometimes|required|string|max:20|unique:students,student_no,' . $id,
            'first_name' => 'sometimes|required|string|max:100',
            'last_name'  => 'sometimes|required|string|max:100',
            'course'     => 'sometimes|required|string|max:50',
            'year_level' => 'sometimes|required|integer|min:1|max:4',
            'email'      => 'nullable|email|max:150',
        ]);

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully.',
            'data' => $student,
        ], 200);
    }

    // DELETE /api/students/{id} — delete a student
    public function destroy(string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully.',
        ], 200);
    }
}