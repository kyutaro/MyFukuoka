<?php

namespace Plugin\MyFukuoka\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MyFukuoka
 */
class MyFukuoka extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $my_fukuoka_contents;

    /**
     * @var string
     */
    private $my_fukuoka_contents_02;

    /**
     * @var string
     */
    private $my_fukuoka_contents_03;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set my_fukuoka_contents
     *
     * @param string $myFukuokaContents
     * @return MyFukuoka
     */
    public function setMyFukuokaContents($myFukuokaContents)
    {
        $this->my_fukuoka_contents = $myFukuokaContents;

        return $this;
    }

    /**
     * Get my_fukuoka_contents
     *
     * @return string 
     */
    public function getMyFukuokaContents()
    {
        return $this->my_fukuoka_contents;
    }

    /**
     * Set my_fukuoka_contents_02
     *
     * @param string $myFukuokaContents02
     * @return MyFukuoka
     */
    public function setMyFukuokaContents02($myFukuokaContents02)
    {
        $this->my_fukuoka_contents_02 = $myFukuokaContents02;

        return $this;
    }

    /**
     * Get my_fukuoka_contents_02
     *
     * @return string 
     */
    public function getMyFukuokaContents02()
    {
        return $this->my_fukuoka_contents_02;
    }

    /**
     * Set my_fukuoka_contents_03
     *
     * @param string $myFukuokaContents03
     * @return MyFukuoka
     */
    public function setMyFukuokaContents03($myFukuokaContents03)
    {
        $this->my_fukuoka_contents_03 = $myFukuokaContents03;

        return $this;
    }

    /**
     * Get my_fukuoka_contents_03
     *
     * @return string 
     */
    public function getMyFukuokaContents03()
    {
        return $this->my_fukuoka_contents_03;
    }

    /**
     * Set del_flg
     *
     * @param integer $delFlg
     * @return MyFukuoka
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer 
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return MyFukuoka
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return MyFukuoka
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }
}
