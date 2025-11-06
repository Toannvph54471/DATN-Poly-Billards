<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class SyncCategoryPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🔄 Bắt đầu đồng bộ giá từ Products sang Categories...\n\n";

        // Lấy tất cả sản phẩm Service (giờ chơi bàn)
        $serviceProducts = Product::where('product_type', 'Service')
            ->whereNotNull('category_id')
            ->get();

        $updated = 0;
        $skipped = 0;

        foreach ($serviceProducts as $product) {
            $category = Category::find($product->category_id);

            if (!$category) {
                echo "⚠️  Không tìm thấy category ID {$product->category_id} cho sản phẩm '{$product->name}'\n";
                $skipped++;
                continue;
            }

            // Cập nhật hourly_rate
            $category->hourly_rate = $product->price;
            $category->save();

            echo "✅ Cập nhật '{$category->name}': {$product->price} đ (từ '{$product->name}')\n";
            $updated++;
        }

        echo "\n📊 Kết quả:\n";
        echo "   - Đã cập nhật: {$updated} categories\n";
        echo "   - Bỏ qua: {$skipped} sản phẩm\n";
        echo "\n✨ Hoàn thành!\n";
    }
}
