<?php
	
	namespace App\Utilities;
	
	use App\Entity\Participation;
	use App\Repository\ParticipationRepository;
	use App\Repository\PaysRepository;
	use Symfony\Component\String\Slugger\AsciiSlugger;
	
	class GestionParticipation
	{
		private $participationRepository;
		private $paysRepository;
		
		public function __construct(ParticipationRepository $participationRepository, PaysRepository $paysRepository)
		{
			$this->participationRepository = $participationRepository;
			$this->paysRepository = $paysRepository;
		}
		
		public function formulaire($request)
		{
			$slugy = new AsciiSlugger();
			$montant = abs(strtoupper($this->validForm($request->get('participation_nombre_place')))) * 80000;
			$id_transaction = time().''.substr(uniqid("",true), -9, 4);
			$status_paiement = "INCONNU";
			
			$participant = [
				'nom' => strtoupper($this->validForm($request->get('participation_nom'))),
				'prenoms' => strtoupper($this->validForm($request->get('participation_prenoms'))),
				'telephone' => strtoupper($this->validForm($request->get('participation_telephone'))),
				'club' => strtoupper($this->validForm($request->get('participation_club'))),
				'pays' => abs($this->validForm($request->get('participation_pays'))),
				'place' => abs(strtoupper($this->validForm($request->get('participation_nombre_place')))),
				'montant' => $montant,
				'id_transaction' => $id_transaction,
				'status_paiement' => $status_paiement
			];
			$slug = $slugy->slug(strtolower($participant['nom'].'-'.$participant['prenoms'].'-'.$participant['telephone']));
			
			// Reference de la transaction
			$last_participation = $this->participationRepository->findOneBy([],['id'=>"DESC"]);
			if ($last_participation) $id_old = $last_participation->getId();
			else $id_old = 1;
			
			$reference = $this->reference($id_old);
			
			// Le nom du pays
			$pays = $this->paysRepository->findOneBy(['id'=>$participant['pays']]);
			
			$verifParticipation = $this->participationRepository->findOneBy(['slug'=>$slug]);
			if (!$verifParticipation){
				$participation = new Participation();
				$participation->setNom($participant['nom']);
				$participation->setPrenoms($participant['prenoms']);
				$participation->setClub($participant['club']);
				$participation->setIdTransaction($participant['id_transaction']);
				$participation->setMontant($participant['montant']);
				$participation->setReference($reference);
				$participation->setSlug($slug);
				$participation->setTel($participant['telephone']);
				$participation->setVille($pays->getNomFrFr());
				$participation->setStatutsTransaction($participant['status_paiement']);
				$participation->setPays($pays->getAlpha2());
				
				$this->participationRepository->add($participation, true);
				
				$amount = (int) $montant/(1 - 0.035);
				$amount = $this->arrondiSuperieur($amount, 5);
				
				$message = [
					'transaction_id' => $id_transaction,
					'amount' => $amount,
					'customer_name' => $participant['nom'],
					'customer_surname' => $participant['prenoms'],
					'customer_phone_number' => $participant['telephone'],
					'customer_city' => $pays->getNomFrFr(),
					'customer_country' => strtoupper($pays->getAlpha2()),
					'status' => true,
					'customer_state'=> 'AZ',
					'customer_id' => 1,
					'customer_email' => 'delrodie@gmail.com',
					'customer_address' => 'Cocody',
					'customer_zip_code' => '00225'
				];
			}else{
				$message = [
					'error' => "erreur",
				];
			}
			
			
			
			
			return $message;
		}
		
		/**
		 * Fonction pour arrondir au supérieur
		 *
		 * @param $nombre
		 * @param $arrondi
		 * @return float|int
		 */
		public function arrondiSuperieur($nombre, $arrondi)
		{
			return ceil($nombre / $arrondi) * $arrondi;
		}
		
		
		/**
		 * @param $donnee
		 * @return string
		 */
		public function validForm($donnee): string
		{
			return htmlspecialchars(stripslashes(trim($donnee)));
			
		}
		
		/**
		 * @param $donne
		 * @return mixed|string
		 */
		public function reference($donne)
		{
			if ($donne < 10){
				$res = '2209000'.$donne;
			}elseif($donne < 100){
				$res = '220900'.$donne;
			}elseif($donne < 1000){
				$res = '22090'.$donne;
			}else{
				$res = '2209'.$donne;
			}
			
			return $res;
		}
	}
