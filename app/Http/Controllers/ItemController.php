<?php

namespace App\Http\Controllers;

use App\Item;
use App\Option;
use App\Variant;
use App\ItemImage;
use App\OptionValue;
use App\ItemTransaction;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function get() {
        return Item::with('restaurant')->with('subCategory')->with('branch')->with('unit')->get();
    }
    public function getById($id) {
        return Item::where('id', $id)->with('restaurant')->with('subCategory')->with('branch')->with('unit')->first();
    }
    public function filterByBranch($id) {
        error_log($id);
        if($id <= 0) {
            return Item::with('restaurant')->with('subCategory')->with('branch')->with('unit')->get();
        } else {
            return Item::where('branch_id', $id)->orWhere('branch_id', null)->with('restaurant')->with('subCategory')->with('branch')->with('unit')->get();
        }
    }
    public function store() {
        try {
            if (request('allowVariant') === false) {
                $item = new Item();
                $item->restaurant_id = 1;
                $item->subCategory_id = request('product')['subCategory_id'];
                if (request('product')['branch_id'] !== 0) {
                    $item->branch_id = request('product')['branch_id'];
                }
                $item->unit_id = request('product')['unit_id'];
                $item->name = request('product')['name'];
                $item->sale_price = request('product')['sale_price'];
                $item->purchase_price = request('product')['purchase_price'];
                $item->stock = request('product')['stock'];
                $item->stock_value = request('product')['stock'] * request('product')['purchase_price'];
                $item->description = request('product')['description'];
                $item->save();
                $itemTransaction = new ItemTransaction();
                $itemTransaction->item_id = $item->id;
                $itemTransaction->type = 0;
                $itemTransaction->date = date("Y/m/d");
                $itemTransaction->quantity = request('product')['stock'];
                $itemTransaction->price = request('product')['purchase_price'];
                $itemTransaction->save();
                return $item;
            } else {
                //create item
                $item = new Item();
                $item->restaurant_id = 1;
                $item->subCategory_id = request('product')['subCategory_id'];
                if (request('product')['branch_id'] !== 0) {
                    $item->branch_id = request('product')['branch_id'];
                }
                $item->unit_id = request('product')['unit_id'];
                $item->name = request('product')['name'];
                $item->description = request('product')['description'];
                $item->save();
                foreach (request('options') as $option) {
                    //create options
                    $newOption = new Option();
                    $newOption->name = $option['name'];
                    $newOption->item_id = $item->id;
                    $newOption->save();
                    //create option values
                    $optionValues = explode(",",$option['values']);
                    foreach ($optionValues as $optionValue) {
                        $newOptionValue = new optionValue();
                        $newOptionValue->name = $optionValue;
                        $newOptionValue->option_id = $newOption->id;
                        $newOptionValue->save();
                    }
                }
                foreach (request('variants') as $variant) {
                    //create variant
                    $newVariant = new Variant();
                    $newVariant->name = $variant['variantName'];
                    $newVariant->item_id = $item->id;
                    $newVariant->sale_price = $variant['variantSalePrice'];
                    $newVariant->purchase_price = $variant['variantPurchasePrice'];
                    $newVariant->stock = $variant['variantStock'];
                    $newVariant->stock_value = $variant['variantStock'] * $variant['variantPurchasePrice'];
                    $newVariant->save();
                    //create product transaction
                    $itemTransaction = new ItemTransaction();
                    $itemTransaction->item_id = $item->id;
                    $itemTransaction->type = 0;
                    $itemTransaction->date = date("Y/m/d");
                    $itemTransaction->quantity = $variant['variantStock'];
                    $itemTransaction->price = $variant['variantPurchasePrice'];
                    $itemTransaction->variant_id = $newVariant->id;
                    $itemTransaction->save();
                }
                return $item;
            }
        } catch (\Throwable $e) {
          error_log($e);
            return response()->json([
                'message' => request()->all(),
            ],200);
        }
    }
    public function update($id) {
        Item::where('id', $id)->update([
            'name' => request('product')['name'],
            'description' => request('product')['description']
        ]);

        if (request('allowVariant') === false) {
            Variant::where('item_id', $id)->update([
                'active' => false
            ]);
            $options = Option::where('item_id',$id)->get();
            foreach($options as $option) {
                OptionValue::where('option_id',$option->id)->delete();
            }
            Option::where('item_id',$id)->delete();
            Item::where('id', $id)->update([
                'name' => request('product')['name'],
                'description' => request('product')['description'],
                'unit_id' => request('product')['unit_id'],
                'sale_price' => request('product')['sale_price'],
                'purchase_price' => request('product')['purchase_price']
            ]);
            ItemTransaction::where('item_id',$id)->whereNotnull('variant_id')->update([
                'active' => false
            ]);
            $opening  = ItemTransaction::where('item_id', $id)->whereNull('variant_id')->where('type',0)->first();
            if ($opening) {
                ItemTransaction::where('item_id', $id)->whereNull('variant_id')->where('type',0)->update([
                    'quantity' => request('product')['stock'],
                    'price' => request('product')['purchase_price']
                ]);
            } else {
                $itemTransaction = new ItemTransaction();
                $itemTransaction->item_id = $id;
                $itemTransaction->type = 0;
                $itemTransaction->date = date("Y/m/d");
                $itemTransaction->quantity = request('product')['stock'];
                $itemTransaction->price = request('product')['purchase_price'];
                $itemTransaction->save();
            }

            $itemTransactions = ItemTransaction::where('item_id', $id)->where('active', 1)->get();
            $stock = 0;
            $stock_value = 0;
            foreach ($itemTransactions as $itemTransaction) {
                if ($itemTransaction->type === 2) {
                    $stock = $stock - $itemTransaction->quantity;
                    $stock_value = $stock_value - ($itemTransaction->quantity * $itemTransaction->price);
                } else {
                    $stock = $stock + $itemTransaction->quantity;
                    $stock_value = $stock_value + ($itemTransaction->quantity * $itemTransaction->price);
                }
            }
            Item::where('id', $id)->update([
                'stock' => $stock,
                'stock_value' => $stock_value
            ]);
            return Item::where('id', $id)->first();
        } else {
            try {
                $item = Item::where('id',$id)->first();
                Item::where('id',$id)->update([
                    'sale_price' => null,
                    'purchase_price' => null,
                    'stock' => null,
                    'stock_value' => null
                ]);
                $options = Option::where('item_id',$id)->get();
                foreach($options as $option) {
                    OptionValue::where('option_id',$option->id)->delete();
                }
                Option::where('item_id',$id)->delete();
                foreach (request('options') as $option) {
                    //create options
                    $newOption = new Option();
                    $newOption->name = $option['name'];
                    $newOption->item_id = $item->id;
                    $newOption->save();
                    //create option values
                    $optionValues = explode(",",$option['values']);
                    foreach ($optionValues as $optionValue) {
                        $newOptionValue = new optionValue();
                        $newOptionValue->name = $optionValue;
                        $newOptionValue->option_id = $newOption->id;
                        $newOptionValue->save();
                    }
                }
                $indexes = [];
                $variants = Variant::where('item_id', $id)->get();
                foreach (request('variants') as $variant) {
                    $flag = true;
                    foreach ($variants as $oldVariant) {
                        if ($oldVariant->name == $variant['variantName']) {
                            array_push($indexes, $oldVariant->id);
                            Variant::where('id',$oldVariant->id)->update([
                                'sale_price' => $variant['variantSalePrice'],
                                'purchase_price' => $variant['variantPurchasePrice'],
                                'active' => true
                            ]);
                            ItemTransaction::where('variant_id', $oldVariant->id)->where('type', 0)->update([
                                'quantity' => $variant['variantStock'],
                                'price' => $variant['variantPurchasePrice']
                            ]);
                            $variantTransactions = ItemTransaction::where('variant_id', $oldVariant->id)->get();
                            $stock = 0;
                            $stock_value = 0;
                            foreach ($variantTransactions as $variantTransaction) {
                                if ($variantTransaction->type === 2) {
                                    $stock = $stock - $variantTransaction->quantity;
                                    $stock_value = $stock_value - ($variantTransaction->quantity * $variantTransaction->price);
                                } else {
                                    $stock = $stock + $variantTransaction->quantity;
                                    $stock_value = $stock_value + ($variantTransaction->quantity * $variantTransaction->price);
                                }
                            }
                            Variant::where('id', $oldVariant->id)->update([
                                'stock' => $stock,
                                'stock_value' => $stock_value
                            ]);
                            error_log('Matched -> Update Values');
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        error_log('Create New Variant Plus Transaction!');
                        //create variant
                        $newVariant = new Variant();
                        $newVariant->name = $variant['variantName'];
                        $newVariant->item_id = $item->id;
                        $newVariant->sale_price = $variant['variantSalePrice'];
                        $newVariant->purchase_price = $variant['variantPurchasePrice'];
                        $newVariant->stock = $variant['variantStock'];
                        $newVariant->stock_value = $variant['variantStock'] * $variant['variantPurchasePrice'];
                        $newVariant->save();
                        //create product transaction
                        $itemTransaction = new ItemTransaction();
                        $itemTransaction->item_id = $item->id;
                        $itemTransaction->type = 0;
                        $itemTransaction->date = date("Y/m/d");
                        $itemTransaction->quantity = $variant['variantStock'];
                        $itemTransaction->price = $variant['variantPurchasePrice'];
                        $itemTransaction->variant_id = $newVariant->id;
                        $itemTransaction->save();
                    }
                }
                foreach ($variants as $oldVariant) {
                    if (! in_array($oldVariant->id, $indexes)) {
                        Variant::where('id', $oldVariant->id)->update([
                            'active' => false
                        ]);
                        error_log('Deactivate not in indexes array');
                    }
                }
            } catch (\Throwable $e) {
                error_log($e);
            }
        }
        return response()->json([
            'message' => 'success',
        ],200);
    }
    public function delete($id) {
        try {
            Item::where('id', $id)->delete();
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Please Delete All Transaction Before Deleting Item.',
            ],200);
        }
    }
}
