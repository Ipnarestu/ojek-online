@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen Pesanan</h1>
            <p class="text-gray-400 mt-1">Kelola semua pesanan yang masuk</p>
        </div>
        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
            📥 Export Data
        </button>
    </div>

    {{-- STATUS FILTER --}}
    <div class="flex gap-2 flex-wrap">
        <a href="?status=all" class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-700 text-gray-300">Semua ({{ $statusCounts['pending'] + $statusCounts['accepted'] + $statusCounts['on_trip'] + $statusCounts['completed'] }})</a>
        <a href="?status=pending" class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/20 text-yellow-400">Pending ({{ $statusCounts['pending'] }})</a>
        <a href="?status=accepted" class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-400">Accepted ({{ $statusCounts['accepted'] }})</a>
        <a href="?status=on_trip" class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-500/20 text-purple-400">On Trip ({{ $statusCounts['on_trip'] }})</a>
        <a href="?status=completed" class="px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400">Completed ({{ $statusCounts['completed'] }})</a>
    </div>

    <div class="bg-gray-800/50 rounded-xl border border-gray-700 overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-700/50 border-b border-gray-700">
                <tr>
                    <th class="px-4 py-3 text-white text-sm">ID</th>
                    <th class="px-4 py-3 text-white text-sm">User</th>
                    <th class="px-4 py-3 text-white text-sm">Driver</th>
                    <th class="px-4 py-3 text-white text-sm">Jemput</th>
                    <th class="px-4 py-3 text-white text-sm">Tujuan</th>
                    <th class="px-4 py-3 text-white text-sm">Harga</th>
                    <th class="px-4 py-3 text-white text-sm">Status</th>
                    <th class="px-4 py-3 text-white text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-b border-gray-700 hover:bg-gray-700/30">
                    <td class="px-4 py-3 text-gray-300">#{{ $order->id }}</td>
                    <td class="px-4 py-3 text-white">{{ $order->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-white">{{ $order->driver->name ?? 'Belum ada' }}</td>
                    <td class="px-4 py-3 text-gray-300 max-w-[150px] truncate">{{ $order->pickup_location }}</td>