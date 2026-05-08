<!-- File: resources/views/academic/class-routine/print-batch.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Routine - {{ $department->department ?? 'N/A' }} - {{ $batch->title ?? 'N/A' }}</title>
    <!-- Tailwind CSS for styling, but with print-friendly adjustments -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            -webkit-print-color-adjust: exact;
        }
        @media print {
            .no-print {
                display: none;
            }
            .page-break {
                page-break-before: always;
            }
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f7fafc;
            font-weight: bold;
        }
    </style>
</head>
<body class="p-8">
    <div class="container mx-auto">
        <div class="text-center mb-8 no-print">
            <h1 class="text-3xl font-extrabold text-gray-800">Class Routine</h1>
            <p class="text-xl text-gray-600 mt-2">Department: {{ $department->department ?? 'N/A' }}</p>
            <p class="text-xl text-gray-600">Batch: {{ $batch->title ?? 'N/A' }}</p>
        </div>

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">Routine for {{ $batch->title ?? 'N/A' }}</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow-md">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 uppercase text-sm text-gray-600 bg-gray-100">Day</th>
                            <th class="py-3 px-4 uppercase text-sm text-gray-600 bg-gray-100">Time</th>
                            <th class="py-3 px-4 uppercase text-sm text-gray-600 bg-gray-100">Subject</th>
                            <th class="py-3 px-4 uppercase text-sm text-gray-600 bg-gray-100">Teacher</th>
                            <th class="py-3 px-4 uppercase text-sm text-gray-600 bg-gray-100">Room</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                            @if (isset($groupedRoutines[$day]))
                                @foreach ($groupedRoutines[$day] as $routine)
                                    <tr>
                                        @if ($loop->first)
                                            <td rowspan="{{ count($groupedRoutines[$day]) }}" class="font-bold text-gray-800 align-top">{{ $day }}</td>
                                        @endif
                                        <td class="text-sm text-gray-700">{{ $routine->start_time }} - {{ $routine->end_time }}</td>
                                        <td class="text-sm text-gray-700">{{ $routine->subject->title }} ({{ $routine->subject->code }})</td>
                                        <td class="text-sm text-gray-700">{{ $routine->teacher->name ?? 'N/A' }}</td>
                                        <td class="text-sm text-gray-700">{{ $routine->room_number }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
