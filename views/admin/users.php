<?php require_once dirname(__DIR__) . '/layouts/admin_header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-amber-100 mb-6 gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Users &amp; Staff</h1>
        <p class="text-sm text-stone-500 mt-1">Promote registered users to Bakers or Administrators, or provision new staff accounts.</p>
    </div>
    <div>
        <button onclick="document.getElementById('createUserModal').classList.toggle('hidden')"
                class="inline-flex items-center gap-1.5 bg-amber-800 hover:bg-amber-900 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow transition cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Provision New Staff
        </button>
    </div>
</div>

<!-- Provision User Form -->
<div id="createUserModal" class="hidden mb-8 bg-white border border-amber-200 rounded-2xl p-6 shadow-md">
    <div class="flex justify-between items-center mb-4 pb-3 border-b border-amber-50">
        <h3 class="font-bold text-stone-900 text-base">Provision New Account</h3>
        <button onclick="document.getElementById('createUserModal').classList.add('hidden')" class="text-stone-400 hover:text-stone-600 font-bold text-lg leading-none">&times;</button>
    </div>
    <form action="<?= url('/admin/users/create') ?>" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">Full Name</label>
            <input type="text" name="full_name" required placeholder="e.g. Maria Clara"
                   class="w-full text-xs px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-700 bg-stone-50">
        </div>
        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">Email Address</label>
            <input type="email" name="email" required placeholder="e.g. baker@hinabi.com"
                   class="w-full text-xs px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-700 bg-stone-50">
        </div>
        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">Temporary Password</label>
            <input type="password" name="password" required minlength="6" placeholder="Min 6 characters"
                   class="w-full text-xs px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-700 bg-stone-50">
        </div>
        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">Assigned Role</label>
            <div class="flex gap-2">
                <select name="role_id" class="w-full text-xs px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-700 bg-white">
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= (int)$r['id'] ?>" <?= ($r['id'] == 1) ? 'selected' : '' ?>>
                            <?= e($r['role_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bg-amber-800 hover:bg-amber-900 text-white font-bold px-4 py-2 rounded-lg text-xs transition whitespace-nowrap cursor-pointer">
                    Save
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Role Summary Cards -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <a href="<?= url('/admin/users') ?>" class="bg-white p-4 rounded-xl border <?= ($roleFilter === null) ? 'border-amber-500 ring-2 ring-amber-400' : 'border-amber-100 hover:border-amber-300' ?> shadow-sm transition">
        <div class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Total Accounts</div>
        <div class="text-2xl font-black text-stone-900 mt-1"><?= (int)($userStats['total_users'] ?? 0) ?></div>
        <div class="text-xs text-amber-800 font-semibold mt-0.5">View all &rarr;</div>
    </a>
    <a href="<?= url('/admin/users?role=1') ?>" class="bg-white p-4 rounded-xl border <?= ($roleFilter === 1) ? 'border-stone-500 ring-2 ring-stone-400' : 'border-amber-100 hover:border-stone-300' ?> shadow-sm transition">
        <div class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Customers</div>
        <div class="text-2xl font-black text-stone-700 mt-1"><?= (int)($userStats['total_customers'] ?? 0) ?></div>
        <div class="text-xs text-stone-500 font-semibold mt-0.5">Filter customers &rarr;</div>
    </a>
    <a href="<?= url('/admin/users?role=2') ?>" class="bg-white p-4 rounded-xl border <?= ($roleFilter === 2) ? 'border-amber-600 ring-2 ring-amber-500' : 'border-amber-100 hover:border-amber-300' ?> shadow-sm transition">
        <div class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Kitchen Bakers</div>
        <div class="text-2xl font-black text-amber-900 mt-1"><?= (int)($userStats['total_bakers'] ?? 0) ?></div>
        <div class="text-xs text-amber-700 font-semibold mt-0.5">Filter bakers &rarr;</div>
    </a>
    <a href="<?= url('/admin/users?role=3') ?>" class="bg-white p-4 rounded-xl border <?= ($roleFilter === 3) ? 'border-orange-500 ring-2 ring-orange-400' : 'border-amber-100 hover:border-orange-200' ?> shadow-sm transition">
        <div class="text-[11px] font-bold uppercase tracking-wider text-orange-700">Administrators</div>
        <div class="text-2xl font-black text-orange-900 mt-1"><?= (int)($userStats['total_admins'] ?? 0) ?></div>
        <div class="text-xs text-orange-700 font-semibold mt-0.5">Filter admins &rarr;</div>
    </a>
</div>

<!-- Search Bar -->
<div class="bg-white rounded-2xl p-4 shadow-sm border border-amber-100 mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
    <form action="<?= url('/admin/users') ?>" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
        <?php if ($roleFilter): ?>
            <input type="hidden" name="role" value="<?= (int)$roleFilter ?>">
        <?php endif; ?>
        <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search name or email..."
               class="px-3 py-2 text-xs border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-700 bg-stone-50 w-full sm:w-64">
        <button type="submit" class="bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold px-4 py-2 rounded-lg transition">
            Search
        </button>
        <?php if (!empty($search) || !empty($roleFilter)): ?>
            <a href="<?= url('/admin/users') ?>" class="text-xs text-stone-400 hover:text-stone-600 px-2">Reset</a>
        <?php endif; ?>
    </form>
    <div class="text-xs text-stone-500">
        Showing <strong><?= count($users) ?></strong> accounts
    </div>
</div>

<!-- User Table -->
<div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-stone-600">
            <thead class="bg-amber-50/70 uppercase text-stone-500 border-b border-amber-100">
                <tr>
                    <th class="px-5 py-3 font-semibold">User</th>
                    <th class="px-5 py-3 font-semibold">Email</th>
                    <th class="px-5 py-3 font-semibold">Role</th>
                    <th class="px-5 py-3 font-semibold">Change Role</th>
                    <th class="px-5 py-3 font-semibold">Joined</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-amber-50">
                <?php if (empty($users)): ?>
                    <tr><td colspan="6" class="px-5 py-10 text-center text-stone-400">No users found matching your search.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <?php
                            $isSelf = ((int)$u['id'] === (int)\App\Core\Session::id());
                            $rId = (int)($u['role_id'] ?? 1);
                            $roleBadge = match($rId) {
                                3 => 'bg-amber-100 text-amber-900 border-amber-200',
                                2 => 'bg-orange-100 text-orange-800 border-orange-200',
                                default => 'bg-stone-100 text-stone-700 border-stone-200'
                            };
                        ?>
                        <tr class="hover:bg-amber-50/40 transition <?= $isSelf ? 'bg-amber-50/60' : '' ?>">
                            <td class="px-5 py-3">
                                <div class="font-bold text-stone-900 text-sm flex items-center gap-1.5">
                                    <?= e($u['full_name']) ?>
                                    <?php if ($isSelf): ?>
                                        <span class="text-[10px] bg-amber-200 text-amber-900 font-bold px-2 py-0.5 rounded-full">You</span>
                                    <?php endif; ?>
                                </div>
                                <span class="text-[10px] text-stone-400 font-mono">ID: #<?= (int)$u['id'] ?></span>
                            </td>
                            <td class="px-5 py-3 font-mono text-stone-600"><?= e($u['email']) ?></td>
                            <td class="px-5 py-3">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider <?= $roleBadge ?>">
                                    <?= e($u['role_name'] ?? 'Customer') ?>
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <?php if ($isSelf): ?>
                                    <span class="text-[11px] text-stone-400 italic">Self-demotion disabled for safety</span>
                                <?php else: ?>
                                    <form action="<?= url('/admin/users/role') ?>" method="POST" class="flex items-center gap-2">
                                        <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                        <select name="role_id" onchange="this.form.submit()"
                                                class="text-xs bg-stone-50 border border-stone-300 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-amber-700">
                                            <?php foreach ($roles as $r): ?>
                                                <option value="<?= (int)$r['id'] ?>" <?= ($rId == (int)$r['id']) ? 'selected' : '' ?>>
                                                    &rarr; <?= e($r['role_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 text-stone-400 text-[11px]">
                                <?= !empty($u['created_at']) ? date('M j, Y', strtotime($u['created_at'])) : '-' ?>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <?php if (!$isSelf): ?>
                                    <form action="<?= url('/admin/users/delete') ?>" method="POST"
                                          onsubmit="return confirm('Delete user \'<?= e(addslashes($u['full_name'])) ?>\'?');" class="inline">
                                        <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold hover:underline text-xs cursor-pointer">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-stone-300 text-xs">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/admin_footer.php'; ?>
