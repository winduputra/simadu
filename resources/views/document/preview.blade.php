<x-app-layout>
    <div class="space-y-4">
        <a href="{{ route('documents.show', $document) }}" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to details
        </a>

        @include('components.document-viewer', [
            'document' => $document,
            'streamUrl' => $streamUrl,
            'downloadUrl' => $downloadUrl,
            'canDownload' => $canDownload,
        ])
    </div>
</x-app-layout>
