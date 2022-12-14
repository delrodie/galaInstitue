<?php

namespace App\Controller;

use App\Repository\ParticipationRepository;
use App\Utilities\CinetPay;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/cinetpay')]
class CinetpayController extends AbstractController
{
	private $participationRepository;
	
	public function __construct(ParticipationRepository $participationRepository)
	{
		$this->participationRepository = $participationRepository;
	}
	
    #[Route('/notify', name: 'app_cinetpay_notify')]
    public function index(Request  $request)
    {
	    //Initialisation
	    $encoders = [new XmlEncoder(), new JsonEncoder()];
	    $normalizers = [new ObjectNormalizer()];
	    $serializer = new Serializer($normalizers, $encoders);
	
	    // Initialisation des variables
	    $id_transaction = $request->get('cpm_trans_id'); //dd(trim($id_transaction));
	    //dd($participation);
		
		if (isset($id_transaction)){
			try {
				$apikey = '738218945615320aa597ff3.35893469';
				$site_id = '444572';
				
				$Cinetpay = new CinetPay($site_id, $apikey);
				
				$Cinetpay->getPayStatus($id_transaction, $site_id); //dd($Cinetpay);
				
				$amount = $Cinetpay->chk_amount;
				$currency = $Cinetpay->chk_currency;
				$message = $Cinetpay->chk_message;
				$code = $Cinetpay->chk_code;
				$metadata = $Cinetpay->chk_metadata;
				
				$log = "User: ".$_SERVER['REMOTE_ADDR']." - ".date("F j, Y, g:i a").PHP_EOL.
					"Code:".$code.PHP_EOL.
					"Message: ".$message.PHP_EOL.
					"Amount: ".$amount.PHP_EOL.
					"currency: ".$currency.PHP_EOL.
					"--------------------------------------".PHP_EOL; //dd($log);
				
				file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);
				
				// On v??rifie que le montant pay?? chez CinetPay correspond ?? notre
				if ($code == '00'){
					$resultat = [
						'message' => 'F??licitation, votre paiment a ??t?? effectu?? avec succ??s',
					];
					//die();
				}else{
					$resultat = [
						'message' => 'Echec, votre paiement a ??chou?? pour cause : '.$message
					];
					//die();
				}
				
				 //dd($participation);
				$participation = $this->participationRepository->findByIdTransaction(trim($id_transaction));
				if ($participation){
					$participation->setStatutsTransaction('VALIDE');
					$participation->setStatus(true);
					$participation->setResponseId($Cinetpay->chk_api_response_id);
					$participation->setOperatorId($Cinetpay->chk_operator_id);
					$participation->setPaymentMethod($Cinetpay->chk_payment_method);
					$participation->setPaymentDate($Cinetpay->chk_payment_date);
					$this->participationRepository->add($participation, true);
					
					$resultat = [
						'message' => "Participation mise a jour avec succ??s"
					];
				}
				
			}catch (\Exception $e){
				$resultat = [
					'message' => 'Erreur :' .$e->getMessage(),
				];
			}
		}else{
			$resultat = [
				"message" => "id_transaction non fourni"
			];
		}
		
		return $resultat;
    
    }
}
