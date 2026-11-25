<?php require_once dirname(__DIR__) . '/layouts/admin_header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-amber-100 mb-6 gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Customer Inquiries</h1>
        <p class="text-sm text-stone-500 mt-1">Review catering requests and feedback submitted through the contact page.</p>
    </div>
    <a href="<?= url('/admin') ?>" class="inline-flex items-center gap-1 text-xs text-stone-700 hover:text-stone-900 font-bold border border-stone-300 bg-white hover:bg-stone-50 px-3 py-2 rounded-lg transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Overview
    </a>
</div>

<!-- Inquiry Cards -->
<div class="space-y-4">
    <?php if (empty($inquiries)): ?>
        <div class="bg-white rounded-2xl border border-amber-100 p-12 text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-amber-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-stone-800">No customer inquiries yet</h3>
            <p class="text-xs text-stone-500 mt-1">Messages submitted from the contact form will appear here.</p>
        </div>
    <?php else: ?>
        <?php foreach ($inquiries as $inq): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-amber-100 p-6 hover:shadow-md transition">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 pb-3 border-b border-amber-50 gap-2">
                    <div>
                        <h3 class="font-bold text-stone-900 text-base">
                            <?= e($inq['name']) ?>
                            <span class="text-xs font-normal text-stone-400 ml-1">&bull; <?= e($inq['email']) ?></span>
                        </h3>
                    </div>
                    <span class="text-xs text-stone-400">
                        <?= !empty($inq['created_at']) ? date('F j, Y, g:i A', strtotime($inq['created_at'])) : 'Recent' ?>
                    </span>
                </div>
                <div class="text-xs sm:text-sm text-stone-700 leading-relaxed whitespace-pre-wrap bg-amber-50/50 p-4 rounded-xl border border-amber-100">
                    <?= e($inq['message']) ?>
                </div>
                <div class="mt-3 flex justify-end">
                    <a href="mailto:<?= e($inq['email']) ?>?subject=Re: Inquiry with Into Hinabi Bakes"
                       class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 hover:text-amber-900 hover:underline">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                        Reply via Email &rarr;
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/layouts/admin_footer.php'; ?>
