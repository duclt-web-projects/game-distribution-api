<?php

if (!function_exists('upload_image')) {
    /**
     * @param $file [tên file trùng tên input]
     * @param array $extend [ định dạng file có thể upload được]
     * @return array|int [ tham số trả về là 1 mảng - nếu lỗi trả về int ]
     */
    function upload_image($file, $folder = '', array $extend = array())
    {
        $code = 1;

        // lay duong dan anh
        $baseFilename = public_path() . '/uploads/' . $_FILES[$file]['name'];

        // thong tin file
        $info = new SplFileInfo($baseFilename);

        // duoi file
        $ext = strtolower($info->getExtension());

        // kiem tra dinh dang file
        if (!$extend) {
            $extend = ['png', 'jpg', 'jpeg'];
        }

        if (!in_array($ext, $extend)) {
            return $data['code'] = 0;
        }

        // Tên file mới
        $nameFile = trim(str_replace('.' . $ext, '', strtolower($info->getFilename())));
        $filename = date('Y-m-d__') . str_slug($nameFile) . '.' . $ext;

        // thu muc goc de upload
        $path = public_path() . '/uploads/' . date('Y/m/d/');
        if ($folder) {
            $path = public_path() . '/uploads/' . $folder . '/' . date('Y/m/d/');
        }

        if (!\File::exists($path)) {
            mkdir($path, 0777, true);
        }

        // di chuyen file vao thu muc uploads
        move_uploaded_file($_FILES[$file]['tmp_name'], $path . $filename);

        $data = [
            'name' => $filename,
            'code' => $code,
            'path_img' => 'uploads/' . $filename
        ];

        return $data;
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }
}

if (!function_exists('pare_url_file')) {
    /**
     * @param string $image
     * @param string $folder
     * @return string
     */
    function pare_url_file(string $image, string $folder = ''): string
    {
        if (!$image) {
            return '/images/no-image.jpg';
        }

        $explode = explode('__', $image);

        if (isset($explode[0])) {
            $time = str_replace('_', '/', $explode[0]);
            return 'uploads/' . ($folder ? $folder . '/' : '') . date('Y/m/d', strtotime($time)) . '/' . $image;
        }

        return '';
    }
}

if (!function_exists('pare_url_file_user')) {
    function pare_url_file_user($image, $folder = '')
    {
        if (!$image) {
            return '/images/default_avatar.png';
        }

        $explode = explode('__', $image);

        if (isset($explode[0])) {
            $time = str_replace('_', '/', $explode[0]);
            return '/uploads/' . $folder . '/' . date('Y/m/d', strtotime($time)) . '/' . $image;
        }
    }
}

if (!function_exists('get_data_user')) {
    function get_data_user($type, $field = 'id')
    {
        return Auth::guard($type)->user() ? Auth::guard($type)->user()->$field : '';
    }
}

if (!function_exists('upload_file_work')) {
    /**
     * @param $file [tên file trùng tên input]
     * @param array $extend [ định dạng file có thể upload được]
     * @return array|int [ tham số trả về là 1 mảng - nếu lỗi trả về int ]
     */
    function upload_file_work($file)
    {
        $code = 1;
        $dir = "uploads/work";

        //$duoi = $file->getClientOriginalExtension();
        $name = str_replace(' ', '', $file->getClientOriginalName());

        // Tên file mới
        $filename = date('Y-m-d__') . $name;

        // thu muc goc de upload
        $path = public_path() . '/' . $dir . '/' . date('Y/m/d');
        if (!\File::exists($path)) {
            mkdir($path, 0777, true);
        }

        // di chuyen file vao thu muc uploads
        $file->move($path, $filename);

        $data = [
            'name' => $filename,
            'code' => $code,
            'path_img' => $dir . '/' . $filename
        ];

        return $data;
    }
}


if (!function_exists('get_file_work')) {
    function get_file_work($file)
    {
        $dir = "uploads/work";
        $explode = explode('__', $file);
        if (isset($explode[0])) {
            return '/' . $dir . '/' . date('Y/m/d', strtotime($explode[0])) . '/' . $file;
        }
    }
}


if (!function_exists('get_data_user')) {
    function get_data_user($type, $field = 'id')
    {
        return Auth::guard($type)->user() ? Auth::guard($type)->user()->$field : '';
    }
}

//THÊM FUNCTION

if (!function_exists('get_data_user')) {
    function get_data_user($type, $field = 'id')
    {
        return Auth::guard($type)->user() ? Auth::guard($type)->user()->$field : '';
    }
}

if (!function_exists('extract_zip_to_tree_structure')) {
    function extract_zip_to_tree_structure($zip)
    {
        $tree  = array(); //empty arrays

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $path = $zip->getNameIndex($i);
            $pathBySlash = array_values(explode('/', $path));
            $c = count($pathBySlash);
            $temp = &$tree;
            for ($j = 0; $j < $c - 1; $j++)
                if (isset($temp[$pathBySlash[$j]]))
                    $temp = &$temp[$pathBySlash[$j]];
                else {
                    $temp[$pathBySlash[$j]] = array();
                    $temp = &$temp[$pathBySlash[$j]];
                }
            if (substr($path, -1) == '/')
                $temp[$pathBySlash[$c - 1]] = array();
            else
                $temp[] = $pathBySlash[$c - 1];
        }

        return $tree;
    }
}
