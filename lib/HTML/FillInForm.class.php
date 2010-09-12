<?php

/** 
* HTML_FillInForm
* PerlのHTML::FillInFormのPHP版です。PHP4でもPHP5でも動作可能です。
* 一部HTML::FillInForm::Liteの機能も実装しています
* メソッド名も機能もそのまま移植しました
* ドキュメントもほぼそのまま移植しました:-)
* 
* 当初PEARのXML_HTMLSaxを使って実装しようかなと思ったのですが、使い勝手が微妙だったので結局自分でパースしました。
* 
* Perl版HTML::FillInFormとの違いとしてfillの引数に渡す値がscalarrefではなくscalarになることです。
* 
* <pre>
*   $fil->fill(array(
*       'scalar' => $html,
*       'fdat'   => $data
*   ));
* </pre>
* 
* 一応Perlとの互換のためscalarrefでも動くようにはなっています。
* 
* このモジュールは、前のページのHTMLフォームからのデータを HTMLの input や textarea や select タグに自動的に挿入します。 
* 有益な適用例の一つはユーザーが必須項目を埋めずにサブミットした後です。 HTML_FillInForm はサブミットされた情報をすべてフォーム要素に含めて、 HTMLフォームを再表示することができます。
* 
* 履歴
* 2007/06/20 ver 0.01 完成
* 2008/02/29 ver 0.02 change: ソースの文字コードをutf8に変更
*                     add: テストコードの追加
*                     add: />に対応(17_xhtml.t.php)
*                     add: type="textfield"に対応(13_textfield.t.php)
*                     add: disable_fieldsオプションの追加(21_disable_fields.t.php)
*                     add: HTML::FillInForm::Liteのescapeオプションの追加(22_escape_option.t.php)
*                     add: selectedのmultipleに対応(04_select.t.php)
*                     add: formのidに対応(11_target.t.php)
*                     fix: type省略時にtext扱いにならないバグ修正(09_default_type.t.php)
*                     fix: fillメソッドを複数回呼べるように変更(07_reuse.t.php)
*                     fix: checkboxのvalueのデフォルト値にonを追加(03_checkbox.t.php)
*                     fix: 値が複数ある場合のバグ修正(02_hidden.t.php)
*                     fix: 同名のnameが存在する場合のバグ修正(12_mult.t.php)
* 2008/03/11 ver 0.03 fix: 各種警告除去
* 2008/03/27 ver 0.04 change: テストコードをTAP仕様に合わせてPerlのproveで実行できるように変更
* 2009/11/11 ver 0.05 change: array_key_existsを可能な限りissetへ変更。
*                     add: テストコードの追加(23_null_check.t.php)
* 
* @author ittetsu miyazaki<ittetsu.miyazaki _at_ gmail.com>
* @version 0.05
* @package HTML
* @access public
*/
class HTML_FillInForm {
    
    var $ignore_fields      = array();
    var $disable_fields     = array();
    var $objects            = array();
    var $object_param_cache = array();
    var $fdat               = array();
    var $attrs              = array();
    var $select             = array();
    var $current_form       = null;
    var $target             = null;
    var $fill_password      = true;
    var $escape             = 'htmlspecialchars';
    
    /**
    * コンストラクタ
    * <pre>
    * $fif =& new HTML::FillInForm;
    * </pre>
    * 新しいFillInFormオブジェクトを生成します
    * @access public
    * @return object HTML_FillInFormオブジェクト
    */
    function HTML_FillInForm () {
    }
    
    /**
    * $htmlに含まれるHTMLフォームを埋めます
    * 
    * $qからのデータで、$hrmlに含まれるHTMLフォームが埋められたものが返ります。 $qはPerlのCGIモジュールのparamのように働くparam()メソッドを持っている必要があります。
    * 
    * <pre>
    * 
    *   $output = $fif->fill(array(
    *       'scalar'  => $html,
    *       'fobject' => $q
    *   ));
    * 
    * </pre>
    * 
    * 配列で複数のオブジェクトを渡すこともできます。
    * 
    * <pre>
    * 
    *   $output = $fif->fill(array(
    *       'scalar'  => $html,
    *       'fobject' => array($q1, $q2),
    *   ));
    * 
    * </pre>
    * 
    * $fdatからのデータで、$htmlに含まれるHTMLフォームが埋められたものが返ります。配列を使って、$fdatを使う複数の値を渡せます。
    * 
    * <pre>
    * 
    *   $output = $fif->fill(array(
    *       'scalar' => $html,
    *       'fdat'   => $fdat
    *   ));
    * 
    * </pre>
    * 
    * また、$htmlのデータは配列やファイルからも読み込ませることができます。
    * 
    * <pre>
    * 
    *   $output = $fif->fill(array(
    *       'array'   => $array_of_lines,
    *       'fobject' => $q
    *   ));
    * 
    * </pre>
    * 
    * および、
    * 
    * <pre>
    * 
    *   $output = $fif->fill(array(
    *       'file'    => 'form.tmpl',
    *       'fobject' => $q
    *   ));
    * 
    * </pre>
    * 
    * HTMLに複数のフォームがあり、二つのうち一つのフォームにしか値を入れたくなければ、ターゲットを特定できます。
    * 
    * <pre>
    * 
    *   $output = $fif->fill(array(
    *       'scalar' => $html,
    *       'fobject' => $q,
    *       'target' => 'form1'
    *   ));
    * 
    * </pre>
    * 
    * 次のものを含むフォームしか埋めません。
    * 
    * <pre>
    * 
    *   <FORM name="form1"> ... </FORM>
    * 
    * </pre>
    * 
    * 注意して下さい。このメソッドは、デフォルトでパスワードフィールドを埋めます。無効にするには、次のものを渡して下さい。
    * 
    * <pre>
    * 
    *   'fill_password' => false
    * 
    * </pre>
    * 
    * いくつかのフィールドを埋めるのを無効にしたいなら、 ignore_fieldsオプションを使って下さい：
    * 
    * <pre>
    * 
    *   $output = $fif->fill(array(
    *       'scalar' => $html,
    *       'fobject' => $q,
    *       'ignore_fields' => array('prev','next')
    *   ));
    * 
    * </pre>
    * 
    * escapeオプションを使えば独自のエスケープ処理をすることができます。デフォルトではhtmlspecialcharsが設定されています。
    * 
    * <pre>
    * 
    *   function my_escape ($str) {
    *       return "[$str]";
    *   }
    *   
    *   $output = $fif->fill(array(
    *       'scalar' => $html,
    *       'fobject' => $q,
    *       'escape' => 'my_escape',
    *   ));
    * 
    * </pre>
    * 
    * また、オブジェクトのメソッドを定義するには以下のように指定してください
    * 
    * <pre>
    * 
    *     'escape' => array($obj,'method'),
    * 
    * </pre>
    * 
    * また、エスケープをさせたくなければnullをセットしてください
    * 
    * <pre>
    * 
    *     'escape' => null,
    * 
    * </pre>
    * 
    * このモジュールは、値にnullをセットしてもフィールドをクリアしません。空の配列か空の文字列を値にセットすれば、フィールドはクリアされます。例えば:
    * 
    * <pre>
    * 
    *   # フォーム要素の foo はそのままで、いじられません
    *   $output = $fif->fill(array(
    *       'scalar' => $html,
    *       'fdat' => array( 'foo' => null )
    *   ));
    * 
    *   # フォーム要素のfoo はクリアされます
    *   $output = $fif->fill(array(
    *       'scalarref' => $html,
    *       'fdat' => array( 'foo' => "" )
    *   ));
    * 
    * </pre>
    * 
    * @access public
    * @param array オプション
    * @return 埋め込んだHTMLデータ
    */
    function fill($option=array()) {
        
        foreach ( array('ignore_fields','disable_fields') as $optkey ) {
            if ( isset($option[$optkey]) ) {
                $fields = is_array($option[$optkey]) ? $option[$optkey] : array($option[$optkey]);
                foreach ( $fields as $field ) {
                    $tmp =& $this->$optkey;
                    $tmp[$field] = 1;
                }
            }
        }
        
        if ( isset($option['fdat']) ) {
            foreach ($option['fdat'] as $key => $val ) {
                if ( isset($this->ignore_fields[$key]) ) continue;
                if ( is_null($val) ) continue;
                $this->fdat[$key] = $val;
            }
        }
        
        if ( isset($option['fobject']) ) {
            $objects = is_array($option['fobject']) ? $option['fobject'] : array(&$option['fobject']);
            foreach ( $objects as $i => $dummy ) {
                if ( !method_exists($objects[$i],'param') ) {
                    trigger_error("HTML_FillInForm->fill called with fobject option, containing object of type " . get_class($objects[$i]) . " which lacks a param() method!",E_USER_ERROR);
                }
                $this->objects[] =& $objects[$i];
            }
        }
        
        if ( isset($option['target']) ) {
            $this->target = $option['target'];
        }
        
        if ( isset($option['fill_password']) ) {
            $this->fill_password = $option['fill_password'];
        }
        
        if ( array_key_exists('escape',$option) ) {
            if ( is_null($option['escape']) ) {
                $this->escape = array(&$this,'_no_escape');
            }
            else {
                $this->escape = $option['escape'];
            }
        }
        
        $html = '';
        
        if ( isset($option['file']) ) {
            $html = function_exists('file_get_contents') ? file_get_contents($option['file']) : implode('',file($option['file']));
        }
        elseif ( isset($option['scalar']) ) {
            $html =& $option['scalar'];
        }
        elseif ( isset($option['scalarref']) ) {
            $html =& $option['scalarref'];
        }
        elseif ( isset($option['array']) ) {
            $html = implode('',$option['array']);
        }
        elseif ( isset($option['arrayref']) ) {
            $html = implode('',$option['arrayref']);
        }
        
        $html = preg_replace_callback('/<\s*(FORM)\s+([^>]*)>.*?<\/\s*FORM\s*>/is', array(&$this,'_parse'), $html);
        
        $this->_init();
        
        return $html;
    }
    
    function _init () {
        $this->ignore_fields      = array();
        $this->disable_fields     = array();
        $this->objects            = array();
        $this->object_param_cache = array();
        $this->fdat               = array();
        $this->select             = array();
        $this->current_form       = null;
        $this->target             = null;
        $this->fill_password      = true;
        $this->escape             = 'htmlspecialchars';
    }
    
    function fill_file ($file,$option=array()) {
        $option['file'] = $file;
        return $this->fill($option);
    }
    
    function fill_scalar ($scalar,$option=array()) {
        $option['scalar'] = $scalar;
        return $this->fill($option);
    }
    
    function fill_array ($array,$option=array()) {
        $option['array'] = $array;
        return $this->fill($option);
    }
    
    function _parse ($matches) {
        list($origtext,$type,$attr) = $matches;
        $attrs = $this->_get_attrs($attr);
        
        if ( 
            isset($attrs['name']) &&
            !empty($this->disable_fields[$attrs['name']]) &&
            empty($attrs['disable'])
        ) $attrs['disable'] = 1;
        
        switch (strtoupper($type)) {
            case 'FORM'    : return $this->_parse_form($origtext,$attrs);
            case 'INPUT'   : return $this->_parse_input($origtext,$attrs);
            case 'SELECT'  : return $this->_parse_select($origtext,$attrs);
            case 'OPTION'  : return $this->_parse_option($origtext,$attrs);
            case 'TEXTAREA': return $this->_parse_textarea($origtext,$attrs);
        }
        
        return $origtext;
    }
    
    function _parse_form($text,&$attrs) {
        $this->object_param_cache = array();
        $this->current_form = isset($attrs['name']) ? $attrs['name'] : null;
        if ( is_null($this->current_form) ) $this->current_form = isset($attrs['id']) ? $attrs['id'] : null;
        if ( !is_null($this->target) ) {
            if ( is_null($this->current_form) || $this->current_form != $this->target ) {
                return $text;
            }
        }
        $text = preg_replace_callback('/<\s*(INPUT|SELECT|OPTION)\s+([^>]+)>/i', array(&$this,'_parse'), $text);
        $text = preg_replace_callback('/<\s*(TEXTAREA)\s+([^>]+)>.*?<\/\s*TEXTAREA\s*>/is', array(&$this,'_parse'), $text);
        return $text;
    }
    
    function _parse_input($text,&$attrs) {
        if ( !isset($attrs['name']) ) return $text;
        $type = isset($attrs['type']) ? strtolower($attrs['type']) : 'text';
        
        switch ($type) {
            case 'password':
                if ( !$this->fill_password ) break;
            case 'text':
            case 'textfield':
            case 'hidden':
                $val =& $this->_get_param_one($attrs['name']);
                if ( is_null($val) ) break;
                $attrs['value'] = call_user_func($this->escape,$val);
                $text = '<input'.$this->_get_attrs_string($attrs).'>';
                break;
            case 'radio':
                $val =& $this->_get_param_one($attrs['name']);
                if ( is_null($val) ) break;
                unset($attrs['checked']);
                if ( strcmp($attrs['value'],call_user_func($this->escape,$val)) == 0 ) {
                    $attrs['checked'] = "checked";
                }
                $text = '<input'.$this->_get_attrs_string($attrs).'>';
                break;
            case 'checkbox':
                $vals =& $this->_get_param($attrs['name']);
                if ( is_null($vals) ) break;
                if ( !is_array($vals) ) $vals = array($vals);
                unset($attrs['checked']);
                if ( !isset($attrs['value']) ) $attrs['value'] = 'on';
                foreach ($vals as $val) {
                    if ( strcmp($attrs['value'],call_user_func($this->escape,$val)) == 0 ) {
                        $attrs['checked'] = "checked";
                        break;
                    }
                }
                $text = '<input'.$this->_get_attrs_string($attrs).'>';
                break;
        }
        
        return $text;
    }
    
    function _parse_select($text,&$attrs) {
        $this->select['name'] = $attrs['name'];
        if ( array_key_exists('multiple',$attrs) ) $this->select['multiple'] = 1;
        return $text;
    }
    
    function _parse_option($text,&$attrs) {
        if ( isset($this->select['multiple']) ) {
            $vals =& $this->_get_param($this->select['name']);
            if ( is_null($vals) ) return $text;
            if ( !is_array($vals) ) $vals = array($vals);
            unset($attrs['selected']);
            foreach ($vals as $val) {
                if ( strcmp($attrs['value'],call_user_func($this->escape,$val)) == 0 ) {
                    $attrs['selected'] = "selected";
                    break;
                }
            }
        }
        else {
            $val =& $this->_get_param_one($this->select['name']);
            if ( is_null($val) ) return $text;
            unset($attrs['selected']);
            if ( strcmp($attrs['value'],call_user_func($this->escape,$val)) == 0 ) {
                $attrs['selected'] = "selected";
            }
        }
        return '<option'.$this->_get_attrs_string($attrs).'>';
    }
    
    function _parse_textarea($text,&$attrs) {
        $val =& $this->_get_param_one($attrs['name']);
        if ( is_null($val) ) return $text;
        return '<textarea'.$this->_get_attrs_string($attrs).'>'.call_user_func($this->escape,$val).'</textarea>';
    }
    
    function _get_attrs_string($attrs) {
        $ret   = '';
        $slash = false;
        foreach ($attrs as $key => $val) {
            if ( $key === '/' ) {
                $slash = true;
                continue;
            }
            $ret .= is_null($attrs[$key]) ? " $key=\"$key\"" : " $key=\"$val\"";
        }
        if ( $slash ) $ret .= ' /';
        return $ret;
    }
    
    function _get_attrs ($attr) {
        $this->attrs = array();
        $ret_attr = preg_replace_callback(
            '/\s*(\w+)\s*=\s*("[^"]*"|\'[^\']*\'|\w+)\s*/',
            array(&$this,'_attrs_callback'),
            $attr
        );
        $attrs = $this->attrs;
        foreach ( preg_split('/\s+/',$ret_attr,-1,PREG_SPLIT_NO_EMPTY) as $value ) {
            $attrs[$value] = null;
        }
        
        return $attrs;
    }
    
    function _attrs_callback ($m) {
        $this->attrs[strtolower($m[1])] = trim($m[2],'\'"');
        return '';
    }
    
    function _no_escape ($str) {
        return $str;
    }
    
    function & _get_param_one ($name) {
        $vals =& $this->_get_param($name);
        $val  =  is_array($vals) ? array_shift($vals) : $vals;
        return $val;
    }
    
    function & _get_param($name) {
        $null = null;
        if ( isset($this->ignore_fields[$name]) ) return $null;
        if ( isset($this->fdat[$name]) ) return $this->fdat[$name];
        if ( isset($this->object_param_cache[$name]) ) return $this->object_param_cache[$name];
        
        foreach ( $this->objects as $i => $dummy ) {
            $v = $this->objects[$i]->param($name);
            if ( is_null($v) ) continue;
            if ( is_array($v) && !count($v) ) continue;
            $this->object_param_cache[$name] = $v;
            return $v;
        }
        
        return $null;
    }
}

