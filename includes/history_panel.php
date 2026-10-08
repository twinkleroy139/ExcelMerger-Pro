<?php
// includes/history_panel.php
if (!empty($isLoggedIn)):
?>
<div class="bg-gray-800/80 border border-cyan-500/30 p-6 rounded-xl shadow-xl">
    <h3 class="text-sm font-semibold mb-3 text-cyan-400 uppercase tracking-wide">Your Private Merge History</h3>
    <?php if(empty($userHistory)): ?>
        <p class="text-xs text-gray-400">No merge logs found for your account yet.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-700 text-cyan-300">
                        <th class="pb-2">Date</th>
                        <th class="pb-2">Files</th>
                        <th class="pb-2">Input Rows</th>
                        <th class="pb-2">Output Rows</th>
                        <th class="pb-2">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50 text-gray-300">
                    <?php foreach($userHistory as $row): ?>
                    <tr>
                        <td class="py-2"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td class="py-2 text-cyan-400"><?= htmlspecialchars($row['total_files']) ?></td>
                        <td class="py-2"><?= htmlspecialchars($row['input_rows']) ?></td>
                        <td class="py-2 text-green-400"><?= htmlspecialchars($row['output_rows']) ?></td>
                        <td class="py-2">
                            <a href="outputs/<?= htmlspecialchars($row['output_file']) ?>" class="text-cyan-400 hover:underline">Download</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>