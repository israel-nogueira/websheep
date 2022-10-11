<?php
    abstract class image{
        private $image;
        private $name;
        
        private $folder;
        private $infoImage;
        private $path;
        
        private $jpegQuality;
        private $pngQuality;
        private $gifQuality;
        
        public function getImage()                  {return $this->image;}
        public function setImage($image)            {$this->image = $image;}
        public function getName()                   {return $this->name;}
        public function setName($name)              {$this->name = $name;}
        public function getFolder()                 {return $this->folder;}
        public function setFolder($folder)          {$this->folder = $folder;}
        public function setInfoImage($infoImage)    {$this->infoImage = getimagesize( $infoImage );}
        public function getInfoImage($index=null)   {return ( is_numeric( $index ) and array_key_exists( $index, $this->infoImage ) ) ? $this->infoImage[$index] : $this->infoImage;}
        public function setJpegQuality($quality)    {$this->jpegQuality = ( $quality > 0 and $quality <= 100 ) ? $quality : "100";}
        public function getJpegQuality()            {return $this->jpegQuality;}
        public function setPngQuality($quality)     {$this->pngQuality = ( $quality > 0 and $quality <= 9 ) ? $quality : "9";}
        public function getPngQuality()             {return $this->pngQuality;}
        public function setGifQuality($quality)     {$this->gifQuality = ( $quality > 0 and $quality <= 100 ) ? $quality : "100";}
        public function getGifQuality()             {return $this->gifQuality;}
        public function getMimeType(){return $this->infoImage['mime'];}
        public function readImage(){
            //se o script estiver na mesma pasta da imagem
            if( file_exists( $this->getImage( ) ) ) {
                $this->setInfoImage( $this->getImage( ) );
            } else {
                //caso contrário, acessar a imagem através do caminho completo (pasta/imagem)
                $this->setInfoImage( $this->getFolder( ) . "/" . $this->getImage( ) );
                $this->setImage( $this->getFolder( ) . "/" . $this->getImage( ) );
            }
        }
    }
    class ThumbBrowser extends image{
        private $showBrowser;
        private $forceDownload;
        private $resource;
        private $NewThumb=null;
        
        public function setNewThumb($name)          {$this->NewThumb = $name;}
        public function getNewThumb()               {return $this->NewThumb;}

        public function showBrowser( $show ){
            $this->showBrowser = (bool) $show;
        }
        
        public function forceDownload( $download ) {
            $this->forceDownload = (bool) $download;
        }
        
        public function prepare( $resource ) {
            $this->resource = $resource;

            if($this->NewThumb!=null) {
                $this->save($this->getNewThumb());
                $this->display();
           }else{
                $this->display();
                return false;
            }

            // if( !$this->showBrowser || $this->showBrowser==false) {
            //     $this->save();
            // } else {
            //     if( $this->forceDownload ) {
            //         $this->download();
            //     } else {
            //         $this->display();
            //     }
            // }
        }
        
        private function output( $path ) {
            switch( image::getMimeType() ) {
                case "image/jpeg": imagejpeg( $this->resource, $path, image::getJpegQuality()); break;
                case "image/png": imagepng( $this->resource, $path, image::getPngQuality()); break;
                case "image/gif": imagegif( $this->resource, $path, image::getGifQuality()); break;
                default: imagejpeg( $this->resource, $path, image::getJpegQuality()); break;
            }
        }
        
        private function download( ) {
            header( "Content-type: " . image::getMimeType() );
            header( "Content-Disposition: attachament; filename = " . $this->resource );
            $this->output( null );
        }
        
        private function display( ) {
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
            clearstatcache();
            header( "Content-type: " . image::getMimeType() );
            header( "Content-Disposition: inline; filename = " . $this->resource );
            imagejpeg( $this->resource,null, 100);
            $this->output( null );
        }
        
        private function save() {
            $this->output( $this->getNewThumb() );
        }
        
        public function __destruct( ) {
            imagedestroy( $this->resource );
        }
    }
    

    class thumb extends ThumbBrowser{
        public $crop;
        public $sufix;
        private $dimensions;
        private $proporcional = true;
        private $x;
        private $y;
        private $stream;
        private $thumbWidth;
        private $thumbHeight;
        private $resource;
        
        public function __construct( $image ) {
            image::setImage( $image );
            image::setJpegQuality( 100 );
            image::setPngQuality( 9 );
            image::setGifQuality( 100 );
            ThumbBrowser::showBrowser( true );
            ThumbBrowser::forceDownload( false );   
        }
        
        public function setDimensions( array $dimensions ) {
            $this->dimensions = $dimensions;
        }
        
        public function getDimensions( ) {
            return $this->dimensions;
        }
        
        public function process( ) {
            image::readImage(); //ler a imagem
            $thumb = null;
            
            //se for múltiplas thumbs
            if( isset( $thumb[0][0] ) ) {
                $thumb = $this->getDimensions();
            } else {
                $thumb[] = $this->getDimensions();
            }

            foreach( $thumb as $t ) {
                
                // altura proporcional
                if( is_numeric( $t[0] ) and empty( $t[1] ) ) {
                    $this->crop = isset( $t[2] ) ? true : false; // caso queira cropar imagens proporcionais, que por padrão são apenas redimensionadas
                    $this->thumbWidth = $t[0];
                    $this->thumbHeight = round( ( image::getInfoImage( 1 ) * $this->thumbWidth ) / image::getInfoImage( 0 ) ); 
                } 
                
                // largura proporcional
                else if( empty( $t[0] ) and is_numeric( $t[1] ) ) {
                    $this->crop = isset( $t[2] ) ? true : false;
                    $this->thumbHeight = $t[1];
                    $this->thumbWidth = round( ( image::getInfoImage( 0 ) * $this->thumbHeight ) / image::getInfoImage( 1 ) ); 
                }
                
                // altura e largura setados
                else if( is_numeric( $t[0] ) and is_numeric( $t[1] ) ) {
                    $this->crop = $this->crop;
                    $this->thumbWidth = $t[0];
                    $this->thumbHeight = $t[1];
                }
                
                // Só gerar a thumb caso a imagem seja grande o suficiente
                // Se a largura da thumb for maior que a original, reduzí-la até o tamanho original
                // Se a altura da thumb for maior que a original, reduzí-la até o tamanho original
                $this->thumbWidth = ( $this->thumbWidth > image::getInfoImage( 0 ) ) ? image::getInfoImage( 0 ) : $this->thumbWidth;
                $this->thumbHeight = ( $this->thumbHeight > image::getInfoImage( 1 ) ) ? image::getInfoImage( 1 ) : $this->thumbHeight;

                $this->makeThumb();
            }
        }
        
        private function sufix( ) {
            //Sufixo do nome do arquivo, se for setado o nome do arquivo é alterado
            if( $this->sufix ) { 
                preg_match( "/(.*)(\.[a-z0-9]+)$/", image::getImage(), $r );
                $nome = $r[1];
                $extensao = $r[2];
                image::setName( $nome . "-" . ceil( $this->thumbWidth ) . "x" . ceil( $this->thumbHeight ) . $extensao );
            } else {
                //sem sufixo, sobrescrever a imagem
                image::setName( image::getImage() );
            }
        }
        
        private function imageCreate( ) {
            switch( image::getMimeType() ){
                case "image/jpeg": $this->stream = imagecreatefromjpeg( image::getImage() ); break;
                case "image/png": $this->stream = imagecreatefrompng( image::getImage() ); break;
                case "image/gif": $this->stream = imagecreatefromgif( image::getImage() ); break;
                default: $this->stream = imagecreatefromjpeg( image::getImage() );
            }
            
            $this->y = imagesy( $this->stream );
            $this->x = imagesx( $this->stream );
                
            //Salvar corretamente as transparências do PNG
            if( image::getMimeType() == 'image/png' ) {
                $trueColor = imageistruecolor( $this->stream );
                
                //Se a imagem é uma True Color
                if( $trueColor ) {
                    $this->resource = imagecreatetruecolor( $this->thumbWidth, $this->thumbHeight );
                    imagealphablending( $this->resource, false );
                    imagesavealpha( $this->resource, true );
                //Se a imagem é indexada
                } else {
                    $this->resource = imagecreate( $this->thumbWidth, $this->thumbHeight );
                    imagealphablending( $this->resource, false );
                    $transparent = imagecolorallocatealpha( $this->resource, 0, 0, 0, 127 );
                    imagefill( $this->resource, 0, 0, $transparent );
                    imagesavealpha( $this->resource, true );
                    imagealphablending( $this->resource, true );
                }
            //Caso for JPG ou GIF (Não funciona com GIF Animado =P)
            } else {
                $this->resource = imagecreatetruecolor( $this->thumbWidth, $this->thumbHeight );
            }
            
            $this->crop();
        }
        
        private function crop( ) {
            //Crop?
            //O crop reduzirá a imagem o máximo possível na horizontal (paisagem) ou na vertical (retrato)
            //baseado na proporção, o menor será reduzido pelo maior. Quando a redução chegar ao máximo, será cortada
            //as sobras a fim de obter o meio da imagem
            if( $this->crop ) {
                //dimensões originais
                $largura_original = image::getInfoImage( 0 );
                $altura_original = image::getInfoImage( 1 );
                
                //Eixos iniciais
                $thumbX = 0;
                $thumbY = 0;
                
                //Largura e altura iniciais
                $thumbLargura = $largura_original;
                $thumbAltura = $altura_original;

                //Proporção inicial (paisagem, retrato ou quadrado)
                $proporcaoX = $largura_original / $this->thumbWidth;
                $proporcaoY = $altura_original / $this->thumbHeight;
    
                //Imagem paisagem
                if( $proporcaoX > $proporcaoY ) {

                    $thumbLargura   = round( $largura_original / $proporcaoX * $proporcaoY );
                    $thumbX         = round( ( $largura_original - ( $largura_original / $proporcaoX * $proporcaoY ) ) / 2 );


                //Imagem retrato
                }else if( $proporcaoY > $proporcaoX ){
                 //   $thumbLargura = round( $largura_original / $proporcaoX * $proporcaoY );
                    $thumbAltura = round( $altura_original / $proporcaoY * $proporcaoX );
                    $thumbY = round( ( $altura_original - ( $altura_original / $proporcaoY * $proporcaoX ) ) / 2 );
                }
                
                //Cropar a imagem no ponto ideal
                imagecopyresampled ( $this->resource, $this->stream, 0, 0, $thumbX, $thumbY, $this->thumbWidth, $this->thumbHeight, $thumbLargura, $thumbAltura);
            } else { 
                //Apenas reajustar imagem
                imagecopyresampled( $this->resource, $this->stream, 0, 0, 0, 0, $this->thumbWidth, $this->thumbHeight, $this->x, $this->y );
            }
            ThumbBrowser::prepare( $this->resource );
        }
        private function makeThumb( ) { 
            //Usuário não quer redimensionar, manter a mesma dimensão
            if( is_null( $this->getDimensions() ) or ( $this->thumbWidth == image::getInfoImage( 0 ) and $this->thumbHeight == image::getInfoImage( 1 ) ) ) {
                $this->thumbWidth = image::getInfoImage( 0 );
                $this->thumbHeight = image::getInfoImage( 1 );
            }
            $this->sufix();
            $this->imageCreate();
        }
    }
    
?>