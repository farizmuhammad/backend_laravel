<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoList;
use App\Models\TodoList;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use stdClass;

class TodoListController extends Controller
{
    use ApiResponse;
    
    /**
     * Display a listing of the resource.
     */
    public function generateExcelReport()
    {
        try {
            $todoLists = TodoList::all();
            return $this->success("Todo Lists", $todoLists);
        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoList $request)
    {
        try {
            $todoList = TodoList::create($request->validated());
            
            if(!$todoList) {
                return $this->error("Failed to add new Todo List", Response::HTTP_BAD_REQUEST);
            }

            return $this->success("Todo List created", $todoList);
        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }


    public function getChartData(Request $request)
    {
        $type = $request->query('type');
        
        try {
            switch ($type) {
                case 'status':
                    $todoLists = TodoList::select("status", DB::raw('COUNT(*) as count'))
                    ->whereIn('status', ['pending', 'open', 'in_progress', 'completed'])
                    ->groupBy('status')
                    ->get();

                    $status_summary = new stdClass();
                    foreach ($todoLists as $key => $todo) {
                        $status_summary->{$todo->status} = $todo->count;
                    }

                    return response()->json([
                        'success' => true,
                        'message' => "Summary data",
                        'status_summary' => $status_summary
                    ], 200);
                    break;
                    
                case 'priority':
                    $todoLists = TodoList::select("priority", DB::raw('COUNT(*) as count'))
                    ->whereIn('priority', ['low', 'medium', 'high'])
                    ->groupBy('priority')
                    ->get();

                    $priority_summary = new stdClass();
                    foreach ($todoLists as $key => $todo) {
                        $priority_summary->{$todo->priority} = $todo->count;
                    }

                    return response()->json([
                        'success' => true,
                        'message' => "Summary data",
                        'priority_summary' => $priority_summary
                    ], 200);
                    break;

                case 'assignee':
                    $todoLists = TodoList::select("assignee", 
                        DB::raw('COUNT(*) as total_todos',
                        'SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) AS total_pending_todos',
                        'SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) AS total_timetracked_completed_todos'),
                    )
                    ->groupBy('assignee')
                    ->get();

                    $assignee_summary = new stdClass();
                    foreach ($todoLists as $key => $todo) {
                        $data = new stdClass();
                        $data->total_todos = $todo->total_todos;
                        $data->total_pending_todos = $todo->total_pending_todos;
                        $data->total_timetracked_completed_todos = $todo->total_timetracked_completed_todos;
                        $assignee_summary->{$todo->assignee} = $data;
                    }

                    return response()->json([
                        'success' => true,
                        'message' => "Summary data",
                        'assignee_summary' => $assignee_summary
                    ], 200);
                    break;
                
                default:
                    return $this->error("Failed to retrieve data. Type not found", Response::HTTP_BAD_REQUEST);
                    break;
            }

        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
