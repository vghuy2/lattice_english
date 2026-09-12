<?php

namespace App\Enums;

enum WritingPromptType: string
{
    // Task 1 Types
    case LINE_GRAPH = 'line_graph';
    case BAR_CHART = 'bar_chart';
    case PIE_CHART = 'pie_chart';
    case TABLE = 'table';
    case MAP = 'map';
    case PROCESS = 'process';
    case MIXED_CHART = 'mixed_chart';

    // Task 2 Types
    case OPINION = 'opinion';
    case DISCUSSION = 'discussion';
    case PROBLEM_SOLUTION = 'problem_solution';
    case ADVANTAGES_DISADVANTAGES = 'advantages_disadvantages';
    case TWO_PART = 'two_part';

    public function label(): string
    {
        return match ($this) {
            self::LINE_GRAPH => 'Line Graph (Biểu đồ đường)',
            self::BAR_CHART => 'Bar Chart (Biểu đồ cột)',
            self::PIE_CHART => 'Pie Chart (Biểu đồ tròn)',
            self::TABLE => 'Table (Bảng biểu số liệu)',
            self::MAP => 'Map (Bản đồ biến đổi địa lý)',
            self::PROCESS => 'Process / Diagram (Quy trình sản xuất/vòng đời)',
            self::MIXED_CHART => 'Multiple / Mixed Charts (Biểu đồ kết hợp)',

            self::OPINION => 'Opinion / Agree or Disagree',
            self::DISCUSSION => 'Discussion / Discuss both views & Give opinion',
            self::PROBLEM_SOLUTION => 'Problems & Solutions / Causes & Solutions',
            self::ADVANTAGES_DISADVANTAGES => 'Advantages & Disadvantages',
            self::TWO_PART => 'Two-part / Direct Questions',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::LINE_GRAPH => 'Line Graph',
            self::BAR_CHART => 'Bar Chart',
            self::PIE_CHART => 'Pie Chart',
            self::TABLE => 'Table',
            self::MAP => 'Map',
            self::PROCESS => 'Process',
            self::MIXED_CHART => 'Mixed Chart',

            self::OPINION => 'Opinion',
            self::DISCUSSION => 'Discussion',
            self::PROBLEM_SOLUTION => 'Problem & Solution',
            self::ADVANTAGES_DISADVANTAGES => 'Adv & Disadv',
            self::TWO_PART => 'Two-part Question',
        };
    }

    public function taskType(): WritingTaskType
    {
        return in_array($this, [
            self::LINE_GRAPH,
            self::BAR_CHART,
            self::PIE_CHART,
            self::TABLE,
            self::MAP,
            self::PROCESS,
            self::MIXED_CHART,
        ]) ? WritingTaskType::TASK_1 : WritingTaskType::TASK_2;
    }

    public function isTask1(): bool
    {
        return $this->taskType() === WritingTaskType::TASK_1;
    }

    public function isTask2(): bool
    {
        return $this->taskType() === WritingTaskType::TASK_2;
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function groupedByTask(): array
    {
        return [
            'Task 1 (Biểu đồ & Quy trình)' => [
                self::LINE_GRAPH->value => self::LINE_GRAPH->label(),
                self::BAR_CHART->value => self::BAR_CHART->label(),
                self::PIE_CHART->value => self::PIE_CHART->label(),
                self::TABLE->value => self::TABLE->label(),
                self::MAP->value => self::MAP->label(),
                self::PROCESS->value => self::PROCESS->label(),
                self::MIXED_CHART->value => self::MIXED_CHART->label(),
            ],
            'Task 2 (Nghị luận xã hội & Quan điểm)' => [
                self::OPINION->value => self::OPINION->label(),
                self::DISCUSSION->value => self::DISCUSSION->label(),
                self::PROBLEM_SOLUTION->value => self::PROBLEM_SOLUTION->label(),
                self::ADVANTAGES_DISADVANTAGES->value => self::ADVANTAGES_DISADVANTAGES->label(),
                self::TWO_PART->value => self::TWO_PART->label(),
            ],
        ];
    }
}
