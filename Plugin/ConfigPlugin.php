<?php
namespace Vexpro\GerminiConfig\Plugin;

class ConfigPlugin
{

    protected $scopeConfig;
    protected $catalogSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Session $catalogSession
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->catalogSession = $catalogSession;
    }

    // Autentica o usuário
    public function authenticate($customerId, $password)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerRegistry = $objectManager->get('Magento\Customer\Model\CustomerRegistry');
        $customerSecure = $customerRegistry->retrieveSecureData($customerId);
        $hash = $customerSecure->getPasswordHash();
        $teste = $this->_encryptor->validateHash($password, $hash);
        if (!$teste) {
            return false;
        }
        return true;
    }

    public function aroundSave(
        \Magento\Config\Model\Config $subject,
        \Closure $proceed
    ) {
        //Get Object Manager Instance
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $messageManager = $objectManager->get('Magento\Framework\Message\ManagerInterface');

        try{
            $login = $subject->get()['groups']['general']['fields']['identity_login']['value'];
        } catch (Exception $e) {
            return $proceed();
        } finally {

            // $messageManager->addSuccess($url_base);

        if (!isset($login))
            return $proceed();

        $password = $subject->get()['groups']['general']['fields']['identity_password']['value'];

        $url_base = $subject->get()['groups']['general']['fields']['identity_url']['value'];

        // $url_base = $this->scopeConfig->getValue('acessos/general/identity_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $response = "";
        $url = $url_base . '/connect/token';
        $params = [
            "username" => $login,
            "password" => $password,
            "client_id" => "ro.client.partner",
            "client_secret" => "secret",
            "grant_type" => "password",
            "scope" => "germini-api openid profile"
        ];

        $data_json = json_encode($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: text/plain'));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response  = curl_exec($ch);
        // $this->_curl->post($url, $params);
        //response will contain the output in form of JSON string
        $resultado = json_decode($response);

        $messageManager->addSuccess($resultado);



        if ($response == "") {
            $messageManager->addError('Usuário não existe no Germini');
            return;
        } else {
            if (isset($resultado->error)) {
                $messageManager->addError('Erro ao conectar com germini');
                return;
            } else {
                $token = json_decode($response)->access_token;
                 // Salva o token em uma variável de sessão
                $this->catalogSession->setData('token', $token);
                $messageManager->addSuccess('Usuário e senha validados com sucesso');
            }
        }
        }

        return $proceed();
    }
}
