<?php require_once dirname(__DIR__) . '/layouts/admin_header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-amber-100 mb-6 gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Product Catalog</h1>
        <p class="text-sm text-stone-500 mt-1">Add cookies and cakes, update prices, descriptions, and publish or unpublish items.</p>
    </div>
    <button onclick="document.getElementById('productFormModal').classList.toggle('hidden')"
            class="inline-flex items-center gap-1.5 bg-amber-800 hover:bg-amber-900 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow transition cursor-pointer">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add New Product
    </button>
</div>

<!-- Add / Edit Product Form -->
<div id="productFormModal" class="hidden mb-8 bg-white border border-amber-200 rounded-2xl p-6 shadow-md">
    <div class="flex justify-between items-center mb-4 pb-3 border-b border-amber-50">
        <h3 id="formTitle" class="font-bold text-stone-900 text-base">Add New Bakery Item</h3>
        <button onclick="document.getElementById('productFormModal').classList.add('hidden')" class="text-stone-400 hover:text-stone-600 font-bold text-lg leading-none">&times;</button>
    </div>
    <form action="<?= url('/admin/products/save') ?>" method="POST" id="productForm" class="space-y-4">
        <input type="hidden" name="id" id="prod_id" value="0">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Product Name</label>
                <input type="text" name="name" id="prod_name" required placeholder="e.g. Pistachio Matcha Cookie"
                       class="w-full text-xs px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-700 bg-stone-50">
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Category</label>
                <select name="category_id" id="prod_category_id"
                        class="w-full text-xs px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-700 bg-white">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"><?= e($cat['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Price (USD)</label>
                <input type="number" step="0.01" name="price" id="prod_price" required placeholder="3.50"
                       class="w-full text-xs px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-700 bg-stone-50">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">Image URL</label>
            <input type="url" name="image_url" id="prod_image_url" required placeholder="https://images.unsplash.com/..."
                   class="w-full text-xs px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-700 bg-stone-50">
        </div>
        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">Description</label>
            <textarea name="description" id="prod_description" rows="2" placeholder="Describe the flavor, texture, and ingredients..."
                      class="w-full text-xs px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-700 bg-stone-50"></textarea>
        </div>
        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-2 text-xs font-bold text-stone-700 cursor-pointer">
                <input type="checkbox" name="is_active" id="prod_is_active" value="1" checked class="rounded text-amber-800 focus:ring-amber-700">
                Active / Published
            </label>
            <label class="flex items-center gap-2 text-xs font-bold text-stone-700 cursor-pointer">
                <input type="checkbox" name="availability" id="prod_availability" value="1" checked class="rounded text-amber-800 focus:ring-amber-700">
                In Stock
            </label>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-amber-50">
            <button type="button" onclick="document.getElementById('productFormModal').classList.add('hidden')"
                    class="px-4 py-2 border border-stone-300 text-stone-600 rounded-lg text-xs font-semibold hover:bg-stone-50 transition">
                Cancel
            </button>
            <button type="submit" class="bg-amber-800 hover:bg-amber-900 text-white font-bold px-6 py-2 rounded-lg text-xs shadow transition cursor-pointer">
                Save Product
            </button>
        </div>
    </form>
</div>

<!-- Category Filter Tabs -->
<div class="flex flex-wrap gap-2 mb-6">
    <a href="<?= url('/admin/products') ?>" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?= ($selectedCategory === null) ? 'bg-stone-900 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' ?>">
        All Items (<?= count($products) ?>)
    </a>
    <?php foreach ($categories as $cat): ?>
        <a href="<?= url('/admin/products?category=' . $cat['id']) ?>" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?= ($selectedCategory == $cat['id']) ? 'bg-stone-900 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' ?>">
            <?= e($cat['category_name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Product Table -->
<div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-stone-600">
            <thead class="bg-amber-50/70 uppercase text-stone-500 border-b border-amber-100">
                <tr>
                    <th class="px-5 py-3 font-semibold">Product</th>
                    <th class="px-5 py-3 font-semibold">Category</th>
                    <th class="px-5 py-3 font-semibold">Price</th>
                    <th class="px-5 py-3 font-semibold">Store Status</th>
                    <th class="px-5 py-3 font-semibold">Stock</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-amber-50">
                <?php if (empty($products)): ?>
                    <tr><td colspan="6" class="px-5 py-10 text-center text-stone-400">No products found in this category.</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="<?= e($p['image_url']) ?>" alt="<?= e($p['name']) ?>"
                                         class="w-11 h-11 rounded-lg object-cover border border-amber-100 flex-shrink-0">
                                    <div>
                                        <div class="font-bold text-stone-900 text-sm"><?= e($p['name']) ?></div>
                                        <div class="text-[11px] text-stone-400 line-clamp-1 max-w-xs"><?= e($p['description'] ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider <?= ($p['category_id'] == 2) ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-900' ?>">
                                    <?= e($p['category_name'] ?? 'Bakery') ?>
                                </span>
                            </td>
                            <td class="px-5 py-3 font-mono font-bold text-stone-900">$<?= number_format((float)($p['price'] ?? 0), 2) ?></td>
                            <td class="px-5 py-3">
                                <?php if (!empty($p['is_active'])): ?>
                                    <span class="text-emerald-700 font-semibold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Published
                                    </span>
                                <?php else: ?>
                                    <span class="text-stone-400 font-semibold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-stone-300 inline-block"></span> Hidden
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3">
                                <?php if (!empty($p['availability'])): ?>
                                    <span class="bg-emerald-100 text-emerald-800 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full">In Stock</span>
                                <?php else: ?>
                                    <span class="bg-rose-100 text-rose-700 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full">Sold Out</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 text-right space-x-3">
                                <button onclick='editProduct(<?= json_encode($p) ?>)'
                                        class="text-amber-800 hover:text-amber-900 font-semibold hover:underline text-xs cursor-pointer">
                                    Edit
                                </button>
                                <form action="<?= url('/admin/products/delete') ?>" method="POST"
                                      onsubmit="return confirm('Delete this product?');" class="inline">
                                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold hover:underline text-xs cursor-pointer">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function editProduct(prod) {
    document.getElementById('productFormModal').classList.remove('hidden');
    document.getElementById('formTitle').textContent = 'Edit Bakery Item';
    document.getElementById('prod_id').value = prod.id;
    document.getElementById('prod_name').value = prod.name;
    document.getElementById('prod_category_id').value = prod.category_id || 1;
    document.getElementById('prod_price').value = prod.price;
    document.getElementById('prod_image_url').value = prod.image_url;
    document.getElementById('prod_description').value = prod.description || '';
    document.getElementById('prod_is_active').checked = Boolean(prod.is_active);
    document.getElementById('prod_availability').checked = Boolean(prod.availability);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?php require_once dirname(__DIR__) . '/layouts/admin_footer.php'; ?>
