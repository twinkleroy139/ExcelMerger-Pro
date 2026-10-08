<?php
// includes/preview_modal.php
?>
<div id="previewModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 border border-cyan-500 w-full max-w-2xl rounded-xl p-6 shadow-2xl relative space-y-4">
        <div class="flex justify-between items-center border-b border-cyan-500/30 pb-3">
            <h3 class="text-cyan-400 text-sm font-bold uppercase tracking-wider">[ Pre-Merge Structural Preview ]</h3>
            <button onclick="closePreviewModal()" class="text-gray-400 hover:text-cyan-300 text-sm font-bold">[ &times; ]</button>
        </div>
        <div id="previewContent" class="space-y-3 max-h-96 overflow-y-auto text-xs">
            <p class="text-cyan-300 animate-pulse">Analyzing payload headers and structure...</p>
        </div>
        <div class="flex justify-end pt-3 border-t border-cyan-500/30 space-x-3">
            <button onclick="closePreviewModal()" class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2 rounded text-xs uppercase tracking-wider">Close Preview</button>
        </div>
    </div>
</div>