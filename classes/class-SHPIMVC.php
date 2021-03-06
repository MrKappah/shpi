<?php
/**
 * TutsupMVC - Gerencia Models, Controllers e Views
 *
 * @package TutsupMVC
 * @since 0.1
 */
class TutsupMVC
{

	/**
	 * $controlador
	 *
	 * Receber� o valor do controlador (Vindo da URL).
	 * exemplo.com/controlador/
	 *
	 * @access private
	 */
	private $controlador;
	
	/**
	 * $acao
	 *
	 * Receber� o valor da a��o (Tamb�m vem da URL):
	 * exemplo.com/controlador/acao
	 *
	 * @access private
	 */
	private $acao;
	
	/**
	 * $parametros
	 *
	 * Receber� um array dos par�metros (Tamb�m vem da URL):
	 * exemplo.com/controlador/acao/param1/param2/param50
	 *
	 * @access private
	 */
	private $parametros;
	
	/**
	 * $not_found
	 *
	 * Caminho da p�gina n�o encontrada
	 *
	 * @access private
	 */
	private $not_found = '/includes/404.php';
	
	/**
	 * Construtor para essa classe
	 *
	 * Obt�m os valores do controlador, a��o e par�metros. Configura 
	 * o controlado e a a��o (m�todo).
	 */
	public function __construct () {
		
		// Obt�m os valores do controlador, a��o e par�metros da URL.
		// E configura as propriedades da classe.
		$this->get_url_data();
		
		/**
		 * Verifica se o controlador existe. Caso contr�rio, adiciona o
		 * controlador padr�o (controllers/home-controller.php) e chama o m�todo index().
		 */
		if ( ! $this->controlador ) {
			
			// Adiciona o controlador padr�o
			require_once ABSPATH . '/controllers/home-controller.php';
			
			// Cria o objeto do controlador "home-controller.php"
			// Este controlador dever� ter uma classe chamada HomeController
			$this->controlador = new HomeController();
			
			// Executa o m�todo index()
			$this->controlador->index();
			
			// FIM :)
			return;
		
		}
		
		// Se o arquivo do controlador n�o existir, n�o faremos nada
		if ( ! file_exists( ABSPATH . '/controllers/' . $this->controlador . '.php' ) ) {
			// P�gina n�o encontrada
			require_once ABSPATH . $this->not_found;
			
			// FIM :)
			return;
		}
				
		// Inclui o arquivo do controlador
		require_once ABSPATH . '/controllers/' . $this->controlador . '.php';
		
		// Remove caracteres inv�lidos do nome do controlador para gerar o nome
		// da classe. Se o arquivo chamar "news-controller.php", a classe dever�
		// se chamar NewsController.
		$this->controlador = preg_replace( '/[^a-zA-Z]/i', '', $this->controlador );
		
		// Se a classe do controlador indicado n�o existir, n�o faremos nada
		if ( ! class_exists( $this->controlador ) ) {
			// P�gina n�o encontrada
			require_once ABSPATH . $this->not_found;

			// FIM :)
			return;
		} // class_exists
		
		// Cria o objeto da classe do controlador e envia os par�mentros
		$this->controlador = new $this->controlador( $this->parametros );
		
		// Remove caracteres inv�lidos do nome da a��o (m�todo)
		$this->acao = preg_replace( '/[^a-zA-Z]/i', '', $this->acao );
		
		// Se o m�todo indicado existir, executa o m�todo e envia os par�metros
		if ( method_exists( $this->controlador, $this->acao ) ) {
			$this->controlador->{$this->acao}( $this->parametros );
			
			// FIM :)
			return;
		} // method_exists
		
		// Sem a��o, chamamos o m�todo index
		if ( ! $this->acao && method_exists( $this->controlador, 'index' ) ) {
			$this->controlador->index( $this->parametros );		
			
			// FIM :)
			return;
		} // ! $this->acao 
		
		// P�gina n�o encontrada
		require_once ABSPATH . $this->not_found;
		
		// FIM :)
		return;
	} // __construct
	
	/**
	 * Obt�m par�metros de $_GET['path']
	 *
	 * Obt�m os par�metros de $_GET['path'] e configura as propriedades 
	 * $this->controlador, $this->acao e $this->parametros
	 *
	 * A URL dever� ter o seguinte formato:
	 * http://www.example.com/controlador/acao/parametro1/parametro2/etc...
	 */
	public function get_url_data () {
		
		// Verifica se o par�metro path foi enviado
		if ( isset( $_GET['path'] ) ) {
	
			// Captura o valor de $_GET['path']
			$path = $_GET['path'];
			
			// Limpa os dados
            $path = rtrim($path, '/');
            $path = filter_var($path, FILTER_SANITIZE_URL);
            
			// Cria um array de par�metros
			$path = explode('/', $path);
			
			// Configura as propriedades
			$this->controlador  = chk_array( $path, 0 );
			$this->controlador .= '-controller';
			$this->acao         = chk_array( $path, 1 );
			
			// Configura os par�metros
			if ( chk_array( $path, 2 ) ) {
				unset( $path[0] );
				unset( $path[1] );
				
				// Os par�metros sempre vir�o ap�s a a��o
				$this->parametros = array_values( $path );
			}
			
			
			// DEBUG
			//
			// echo $this->controlador . '<br>';
			// echo $this->acao        . '<br>';
			// echo '<pre>';
			// print_r( $this->parametros );
			// echo '</pre>';
		}
	
	} // get_url_data
	
} // class TutsupMVC