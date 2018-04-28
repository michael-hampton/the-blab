<?php

use Phalcon\Mvc\View;

class ProductController extends ControllerBase
{

    public function indexAction ()
    {

        Phalcon\Tag::setTitle ("Shop");

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);
            $arrCategories = (new ProductCategoryFactory())->getCategories ();
            $arrFavorites = (new FavoriteFactory())->getFavoritesForUser ($objUser, new ProductFactory ());
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrCategories === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get categories"]
                            ]
            );
        }

        if ( $arrFavorites === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get users favorites"]
                            ]
            );
        }

        $this->view->arrCategories = $arrCategories;
        $this->view->arrFavorites = $arrFavorites;
    }

    public function getUsersFavoritesAction ()
    {
        $this->view->disable ();

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objUser = new User ($_SESSION['user']['user_id']);
            $arrFavorites = (new FavoriteFactory())->getFavoritesForUser ($objUser, new ProductFactory ());
            $objUploadFactory = new UploadFactory();
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $arrFavorites === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( !empty ($arrFavorites) )
        {
            $arrData = ["count" => count ($arrFavorites)];

            foreach ($arrFavorites as $objFavorite) {

                $arrImages = $objUploadFactory->getImagesForProduct ($objFavorite);

                if ( !empty ($arrImages) && count ($arrImages) > 0 )
                {
                    $image = $arrImages[0]->getFileLocation ();
                }
                else
                {
                    $image = '';
                }


                $arrData['products'][] = array(
                    "product_name" => $objFavorite->getName (),
                    "seller" => $objFavorite->getSeller (),
                    "id" => $objFavorite->getId (),
                    "user_id" => $objFavorite->getUserId (),
                    "description" => $objFavorite->getDescription (),
                    "image" => $image
                );
            }
        }

        $this->ajaxresponse ("success", "success", $arrData);
    }

    public function saveNewCategoryAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['id']) )
        {
            $this->ajaxresponse ("error", "Missing data");
        }

        if ( !isset ($_POST['category_name']) )
        {
            $this->ajaxresponse ("error", "Missing data");
        }

        if ( is_numeric ($_POST['id']) )
        {
            $objCategory = new ProductCategory ($_POST['id']);
            $objCategory->setName ($_POST['category_name']);
            $blResponse = $objCategory->save ();
        }
        else
        {
            $objCategoryFactory = new ProductCategoryFactory();
            $blResponse = $objCategoryFactory->createCategory ($_POST['category_name']);
        }

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", "Unable to save category");
        }

        $this->ajaxresponse ("success", "success");
    }

    public function saveNewProductAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['pname']) ||
                !isset ($_POST['pprice']) ||
                !isset ($_POST['pcode']) ||
                !isset ($_POST['pcolor']) ||
                !isset ($_POST['psize']) ||
                !isset ($_POST['pcategory']) ||
                !isset ($_POST['pdescription']) )
        {
            $this->ajaxresponse ("error", "Missing data");
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid User");
        }

        $objProductFactory = new ProductFactory();
        $objProduct = $objProductFactory->createProduct (
                new User ($_SESSION['user']['user_id']), new ProductCategory ($_POST['pcategory']), $_POST['psize'], $_POST['pcolor'], $_POST['pcode'], $_POST['pname'], $_POST['pdescription'], 'Test', $_POST['pprice']
        );

        if ( $objProduct === false )
        {
            $this->ajaxresponse ("error", "Unable to save product");
        }

        $objUpload = new UploadFactory();

        if ( !empty ($_FILES[0]) )
        {

            foreach ($_FILES as $arrFile) {
                if ( !empty ($arrFile['name']) )
                {
                    $target_dir = $this->rootPath . "/blab/public/uploads/products/";

                    $arrIds = [];

                    $target_file = $target_dir . basename ($arrFile["name"]);
                    $uploadOk = 1;
                    $imageFileType = strtolower (pathinfo ($target_file, PATHINFO_EXTENSION));

                    if ( $arrFile["size"] > 500000 )
                    {
                        $this->ajaxresponse ("error", "file is to big");
                        $uploadOk = 0;
                    }

                    // Allow certain file formats
                    if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
                    {
                        $this->ajaxresponse ("error", "file is wrong type");
                        $uploadOk = 0;
                    }

                    $check = getimagesize ($arrFile["tmp_name"]);

                    if ( $check !== false )
                    {
                        $uploadOk = 1;
                    }
                    else
                    {
                        $this->ajaxresponse ("error", "unable to upload file");
                        $uploadOk = 0;
                    }

                    if ( move_uploaded_file ($arrFile["tmp_name"], $target_file) )
                    {
                        $savedLocation = str_replace ($this->rootPath, "", $target_file);

                        $objUploaded = $objUpload->createUpload (new User ($_SESSION['user']['user_id']), $savedLocation);

                        if ( $objUploaded === false )
                        {
                            $this->ajaxresponse ("error", "unable to save image");
                            return false;
                        }

                        $blResult = $objProduct->saveProductImage ($objUploaded);

                        if ( $blResult === false )
                        {
                            $this->ajaxresponse ("error", "Unable t save product image");
                        }
                    }
                }
            }
        }

        $this->ajaxresponse ("success", "success");
    }

    public function editProductAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_POST['id']) )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid id"]
                            ]
            );
        }

        $objProduct = new Product ($_POST['id']);

        $arrCategories = (new ProductCategoryFactory())->getCategories ();

        if ( $arrCategories === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get categories"]
                            ]
            );
        }

        $arrProductImages = (new UploadFactory())->getImagesForProduct ($objProduct);

        if ( $arrProductImages === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unbale to get product images"]
                            ]
            );
        }

        $this->view->arrProductImages = $arrProductImages;

        $this->view->arrCategories = $arrCategories;

        $this->view->objProduct = $objProduct;
    }

    public function saveUpdatedProductAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['product_id']) ||
                !isset ($_POST['pname']) ||
                !isset ($_POST['pprice']) ||
                !isset ($_POST['pcode']) ||
                !isset ($_POST['pcolor']) ||
                !isset ($_POST['psize']) ||
                !isset ($_POST['pcategory']) ||
                !isset ($_POST['pdescription']) )
        {
            $this->ajaxresponse ("error", "Missing data");
        }

        $objProduct = new Product ($_POST['product_id']);
        $objProduct->setCategory ($_POST['pcategory']);
        $objProduct->setColour ($_POST['pcolor']);
        $objProduct->setDescription ($_POST['pdescription']);
        $objProduct->setName ($_POST['pname']);
        $objProduct->setPrice ($_POST['pprice']);
        $objProduct->setProductCode ($_POST['pcode']);
        $objProduct->setSize ($_POST['psize']);

        $blResult = $objProduct->save ();

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", "Unable to save product");
        }

        $this->ajaxresponse ("success", "success");
    }

    public function deleteProductAction ()
    {
        $this->view->disable ();

        if ( !isset ($_POST['id']) || trim ($_POST['id']) === "" || !is_numeric ($_POST['id']) )
        {
            $this->ajaxresponse ("error", "Invalid id");
        }

        $objProduct = new Product ($_POST['id']);
        $blResult = $objProduct->delete ();

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", "Unable to delete product");
        }

        $this->ajaxresponse ("success", "success");
    }

    public function sendMessageToSellerAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['userId']) || empty ($_POST['message']) || empty ($_POST['productId']) )
        {
            $this->ajaxresponse ("error", "Missing data");
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid user");
        }

        try {
            $objProduct = new Product ($_POST['productId']);
            $objMessage = new MessageFactory();

            $objUser = new User ($_SESSION['user']['user_id']);

            $message = $objUser->getUsername () . " sent you a message about your product " . $objProduct->getName ();
            $message .= "<br><br>" . $_POST['message'];

            $blResult = $objMessage->sendMessage ($message, new User ($_POST['userId']), $objUser, "", "text");
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResult === false )
        {
            $this->ajaxresponse ("error", "Unable to send message");
        }

        $this->ajaxresponse ("success", "Success");
    }

    public function searchProductsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Invalid user"]
                            ]
            );
        }

        $objUser = new User ($_SESSION['user']['user_id']);

        if ( !isset ($_POST['searchText']) || !isset ($_POST['category_id']) || !isset ($_POST['page_id']) )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Missing required data"]
                            ]
            );
        }

        $vpb_page_limit = 20; //This is the number of contents to display on each page

        try {
            $searchText = trim ($_POST['searchText']) !== "" ? $_POST['searchText'] : null;
            $objCategory = trim ($_POST['category_id']) !== "" && is_numeric ($_POST['category_id']) ? new ProductCategory ($_POST['category_id']) : null;
            $pageId = trim ($_POST['page_id']) !== "" && is_numeric ($_POST['page_id']) ? $_POST['page_id'] : null;

            $objProductFactory = new ProductFactory();

            $arrProducts = $objProductFactory->getProducts (null, $searchText, $objCategory, null, $pageId, $vpb_page_limit, "name", "ASC");
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return false;
        }


        if ( $arrProducts === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get products"]
                            ]
            );
        }

        $arrFavorites = (new FavoriteFactory())->getFavoritesForUser ($objUser, new ProductFactory ());

        if ( $arrFavorites === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get favorites for user"]
                            ]
            );
        }

        $arrFavoritesIds = [];

        if ( !empty ($arrFavorites) )
        {
            foreach ($arrFavorites as $objFavorite) {
                $arrFavoritesIds[] = $objFavorite->getId ();
            }
        }

        $this->view->arrFavoritesIds = $arrFavoritesIds;

        $arrTags = (new TagFactory())->getTagsForShop ();

        if ( $arrTags === false )
        {
            return $this->dispatcher->forward (
                            [
                                "controller" => "issue",
                                "action" => "handler",
                                "params" => ["message" => "Unable to get tags"]
                            ]
            );
        }

        $this->view->arrTags = $arrTags;

        $this->view->objProductFactory = $objProductFactory;
        $this->view->arrProducts = $arrProducts;
        $this->view->pageLimit = $vpb_page_limit;
    }

    public function adminAction ()
    {
        
    }

    public function addProductAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        $arrCategories = (new ProductCategoryFactory())->getCategories ();

        if ( $arrCategories === false )
        {
            $this->ajaxresponse ("error", "Unable to get categories");
        }

        $this->view->arrCategories = $arrCategories;
    }

    public function getCategoriesAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        $objCategoryFactory = new ProductCategoryFactory();

        $arrCategories = $objCategoryFactory->getCategories (null, null, null, null);

        if ( $arrCategories === false )
        {
            $this->ajaxresponse ("error", "Unable to get categories");
        }

        $this->view->arrCategories = $arrCategories;
    }

    public function changePhotoAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['product_id']) || empty ($_POST['photo_id']) || empty ($_FILES[0]) )
        {
            $this->ajaxresponse ("error", "Missing data");
        }

        $arrFile = $_FILES[0];

        $objUpload = new Upload ($_POST['photo_id']);

        $fullPath = $this->rootPath . $objUpload->getFileLocation ();

        unlink ($fullPath);

        $target_dir = $this->rootPath . "/blab/public/uploads/products/";


        $target_file = $target_dir . basename ($arrFile["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower (pathinfo ($target_file, PATHINFO_EXTENSION));

        if ( $arrFile["size"] > 500000 )
        {
            $this->ajaxresponse ("error", "file is to big");
        }

        // Allow certain file formats
        if ( $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
        {
            $this->ajaxresponse ("error", "file is wrong type");
        }

        $check = getimagesize ($arrFile["tmp_name"]);

        if ( $check !== false )
        {
            $uploadOk = 1;
        }
        else
        {
            $this->ajaxresponse ("error", "unable to upload file");
        }

        if ( !move_uploaded_file ($arrFile["tmp_name"], $fullPath) )
        {
            $this->ajaxresponse ("error", "Unable to upload file");
        }

        $this->ajaxresponse ("success", "success", ["location" => $objUpload->getFileLocation ()]);
    }

    public function deleteImageAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['photo_id']) || empty ($_POST['product_id']) )
        {
            $this->ajaxresponse ("error", "Missing data");
        }

        $objUpload = new Upload ($_POST['photo_id']);
        $blResponse = $objUpload->deleteProductImage ();

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", "Unable to delete image");
        }

        $this->ajaxresponse ("success", "success");
    }

    public function manageProductsAction ()
    {
        $this->view->setRenderLevel (View::LEVEL_ACTION_VIEW);

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", "Invalid user");
        }


        $vpb_page_limit = 1; //This is the number of contents to display on each page

        try {
            $pageId = trim ($_POST['page_id']) !== "" && is_numeric ($_POST['page_id']) ? $_POST['page_id'] : null;
            $objCategory = trim ($_POST['category_id']) !== "" && is_numeric ($_POST['category_id']) ? new ProductCategory ($_POST['category_id']) : null;

            $objProductFactory = new ProductFactory();

            $arrProducts = $objProductFactory->getProducts (new User ($_SESSION['user']['user_id']), null, $objCategory, null, $pageId, $vpb_page_limit, "name", "ASC");
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            return false;
        }


        if ( $arrProducts === false )
        {
            $this->ajaxresponse ("error", "Unable to get products");
        }

        $this->view->objProductFactory = $objProductFactory;
        $this->view->arrProducts = $arrProducts;
        $this->view->pageLimit = $vpb_page_limit;
    }

    public function addProductToFavoritesAction ()
    {
        $this->view->disable ();

        if ( empty ($_POST['productId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objFavoriteFactory = new FavoriteFactory();
            $objUser = new User ($_SESSION['user']['user_id']);
            $objProductFactory = new ProductFactory();
            $blResponse = $objFavoriteFactory->saveFavoriteForUser ($objUser, new Product ($_POST['productId']));
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResponse === false )
        {
            $arrErrors = $objFavoriteFactory->getValidationFailures ();

            if ( !empty ($arrErrors) )
            {
                $messaage = implode ("<br/>", $arrErrors);
            }
            else
            {
                $messaage = "Unable to save product to your favorites list";
            }

            $this->ajaxresponse ("error", $messaage);
        }

        $arrFavorites = $objFavoriteFactory->getFavoritesForUser ($objUser, $objProductFactory);

        if ( $arrFavorites === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success", ["count" => count ($arrFavorites)]);
    }

    public function deleteFromFavoritesAction ()
    {

        $this->view->disable ();

        if ( empty ($_POST['productId']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( empty ($_SESSION['user']['user_id']) )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        try {
            $objFavorite = new DeleteFavorite();
            $objUser = new User ($_SESSION['user']['user_id']);
            $blResponse = $objFavorite->deleteFavoriteFromDatabase ($objUser, new Product ($_POST['productId']));
        } catch (Exception $ex) {
            trigger_error ($ex->getMessage (), E_USER_WARNING);
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        if ( $blResponse === false )
        {
            $this->ajaxresponse ("error", $this->defaultErrrorMessage);
        }

        $this->ajaxresponse ("success", "success");
    }

}
