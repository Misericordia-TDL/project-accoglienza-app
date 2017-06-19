<?php
/**
 * Copyright (c) 2017. This file belongs to Misericordia di "Torre del lago Puccini"
 *
 * This class will update the data from an operator
 */

namespace App\Operator\Actions;

use App\Core\Actions\UpdateAction;
use App\Operator\Model\Operator;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class UpdateOperatorAction
 * @package App\Operator\Actions
 * @author Javier Mellado <sol@javiermellado.com>
 */
final class UpdateOperatorAction extends UpdateAction
{

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {

        try {
            //get id from url
            $id = $request->getAttribute('id');
            /** @var  Operator $originalOperator */
            $originalOperator = $this->repository->findById($id);


            $validation = $this->validator->validate($request, [
                'email' => v::noWhitespace()->notEmpty()->email()->EmailEditable($originalOperator->email),
                'name' => v::notEmpty()->alpha()->length(2, 20),
                'surname' => v::notEmpty()->alpha()->length(2, 20),
                'phonenumber' => v::noWhitespace()->notEmpty()->numeric()->phone(),
                'operator_level_id' => v::noWhitespace()->notEmpty()->OperatorLevelValid(),
            ]);

            //If validation fails, return to edit form with error messages embeded in the view
            if ($validation->failed()) {
                $this->flash->addMessage('error', 'Operator data is not correct');
                return $response->withRedirect($this->router->pathFor('edit-operator', ['id' => $id]));
            }

            $originalOperator->update($request->getParams());

            $this->flash->addMessage('info', 'Operator updated correctly');

            return $response->withRedirect($this->router->pathFor('list-operator'));
        } catch (\InvalidArgumentException $e) {
            return $response->withRedirect($this->router->pathFor('list-operator'));
        }
    }
}