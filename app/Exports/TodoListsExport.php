<?php

namespace App\Exports;

use App\Models\TodoList;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TodoListsExport implements FromCollection, WithHeadings,  ShouldAutoSize
{
    protected $title;
    protected $assignee;
    protected $status;
    protected $priority;
    protected $start;
    protected $end;
    protected $min;
    protected $max;

    public function __construct($title = null, $assignee = null, $status = null, $priority = null, $start = null, $end = null, $min = null, $max = null)
    {
        $this->title = $title;
        $this->assignee = $assignee;
        $this->status = $status;
        $this->priority = $priority;
        $this->start = $start;
        $this->end = $end;
        $this->min = $min;
        $this->max = $max;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection($title = null, $assignee = null, $status = null, $priority = null, $start = null, $end = null, $min = null, $max = null)
    public function collection()
    {
        // dd($this);
        $query = TodoList::select('title', 'assignee', 'due_date', 'time_tracked', 'status', 'priority');
        if ($this->title) {
            $query->where('title', 'like', '%' . $this->title . '%');
        }

        if ($this->assignee) {
            $query->where('assignee', 'like', '%' . $this->assignee . '%');
        }

        if ($this->min && $this->max){
            $query->whereBetween('time', [$this->min, $this->max]);
        }

        if ($this->start && $this->end){
            $query->whereBetween('due_date', [$this->start, $this->end]);
        }

        if ($this->priority) {
            $query->whereIn('priority', $this->priority);
        }

        if ($this->status) {
            $query->whereIn('status', $this->status);
        }

        $todos = $query->get();
        
        $total_time = $todos->sum('time_tracked');
        $total_todos = $todos->count();
        $index = 1;

        $rows = $todos->map(function ($item) use (&$index) {
            return [
                $index++,
                $item->title,
                $item->assignee,
                $item->due_date,
                $item->time_tracked,
                $item->status,
                $item->priority,
            ];
        });

        $rows->push([
            '', 'Total number of todos: ', $total_todos
        ]);

        $rows->push([
            '', 'Total tracked time: ', $total_time
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return ['#', 'title', 'assignee', 'due_date', 'time_tracked', 'status', 'priority'];
    }
}
