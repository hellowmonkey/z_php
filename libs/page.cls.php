<?php  

defined('V') OR exit('非法访问');

    class Page {  
          private $total;      //总记录  
          private $pagesize;    //每页显示多少条  
          private $limit;          //limit  
          private $page;           //当前页码  
          private $pagenum;      //总页码  
          private $url;           //地址  
          private $bothnum;      //两边保持数字分页的量  
    
      //构造方法初始化  
      public function __construct($_url,$_total, $_pagesize) {  
         $this->total = $_total ? $_total : 1;  
         $this->pagesize = $_pagesize;  
         $this->pagenum = ceil($this->total / $this->pagesize);  
         $this->page = $this->setPage();  
         $this->limit = "LIMIT ".($this->page-1)*$this->pagesize.",$this->pagesize";  
         $this->url = $_url;  
         $this->bothnum = 2;  
      }  
    
      //拦截器  
      public function __get($_key) {  
         return $this->$_key;  
      }  
    
      //获取当前页码  
      private function setPage() {  
         if (!empty($_GET['page'])) {  
                if ($_GET['page'] > 0) {  
                   if ($_GET['page'] > $this->pagenum) {  
                          return $this->pagenum;  
                   } else {  
                          return $_GET['page'];  
                   }  
                } else {  
                   return 1;  
                }  
         } else {  
                return 1;  
         }  
      }   
    
  //数字目录  
      private function pageList() {  
         for ($i=$this->bothnum;$i>=1;$i--) {  
            $_page = $this->page-$i;  
            if ($_page < 1) continue;  
                $_pagelist .= ' <a class="z-btn" href="'.$this->url.'page='.$_page.'">'.$_page.'</a> ';  
         }  
         $_pagelist .= ' <span class="z-active z-btn">'.$this->page.'</span> ';  
         for ($i=1;$i<=$this->bothnum;$i++) {  
            $_page = $this->page+$i;  
                if ($_page > $this->pagenum) break;  
                $_pagelist .= ' <a class="z-btn" href="'.$this->url.'page='.$_page.'">'.$_page.'</a> ';  
         }  
         return $_pagelist;  
      }  
    
      //首页  
      private function first() {  
         if ($this->page > $this->bothnum+1) {  
                return ' <a class="z-btn" href="'.$this->url.'">1</a><span class="z-btn">...</span>';  
         }  
      }  
    
      //上一页  
      private function prev() {  
         if ($this->page == 1) {  
                return '<span class="z-btn">上一页</span>';  
         }  
         return ' <a class="z-btn" href="'.$this->url.'page='.($this->page-1).'">上一页</a> ';  
      }  
    
      //下一页  
      private function next() {  
         if ($this->page == $this->pagenum) {  
                return '<span class="z-btn">下一页</span>';  
         }  
         return ' <a class="z-btn" href="'.$this->url.'page='.($this->page+1).'">下一页</a> ';  
      }  
    
      //尾页  
      private function last() {  
         if ($this->pagenum - $this->page > $this->bothnum) {  
                return '<span class="z-btn">...</span><a class="z-btn" href="'.$this->url.'page='.$this->pagenum.'">'.$this->pagenum.'</a> ';  
         }  
      }  
    
      //分页信息  
      public function showpage() {  
         $_page .= $this->first();  
         $_page .= $this->pageList();  
         $_page .= $this->last();  
         $_page .= $this->prev();  
         $_page .= $this->next();  
         return $_page;  
      }  
 }  
?>  