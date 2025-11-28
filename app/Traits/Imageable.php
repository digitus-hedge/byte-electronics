<?php

namespace App\Traits;

trait Imageable
{
    public function  storeMedia($request, $path, $type = null)
    {
        // dd($path);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        if ($type == 'banner') {
            $file = $request->file('banner');
        } elseif ($type == 'secondary_logo') {
            $file = $request->file('secondary_logo');
        } else {
            $file = $request->file('image');
        }

        $fileName = uniqid() . '_' . trim($file->getClientOriginalName());
        if ($type == 'banner') {
           
            $this->banner = $fileName;
             
        } elseif ($type == 'secondary_logo') {
            $this->secondary_logo = $fileName;
        } elseif ($type == 'subcat') {
            $this->image = $fileName;
        } else {
            //dd($fileName);
            $this->file_name = $fileName;
            $this->org_name = $file->getClientOriginalName();
            $this->type = $file->getClientOriginalExtension();
            // dd($this,'path :'.$path,'type :'.$type);
        }
        // $this->file_name = $fileName;
        // $this->org_name = $file->getClientOriginalName();
        // $this->type = $file->getClientOriginalExtension();
        $this->save();
        $file->move($path, $fileName);
        return $this;
    }

    public function storeMediaDefaultImage($request, $path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $file = $request->file('default_image');
        $fileName = uniqid() . '_' . trim($file->getClientOriginalName());
        // if()
        $this->file_name_default = $fileName;
        $this->org_name_default = $file->getClientOriginalName();
        $this->type = $file->getClientOriginalExtension();
        $this->save();
        $file->move($path, $fileName);
        return $this;
    }
}
