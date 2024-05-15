<?php declare(strict_types=1);

Namespace EmmetBlueMiddleware\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PermissionGatewayMiddleware implements \EmmetBlueMiddleware\MiddlewareInterface
{
	protected static function isUserLoggedIn($userToken)
    {
       // return true;
	}

	protected static function isUserPermitted($userToken, $resource, $permission)
	{
		
	}

	public function getStandardResponse()
	{
		return function(RequestInterface $request, ResponseInterface $response, callable $next)
		{
			$args = $request->getAttribute('routeInfo')[2];

			$module = $args['module'];
			$resource = $args['resource'];
			
			$whitelists = (new RequestValidatorMiddleware())::$requestActions;
			$permission = ($whitelists)[$request->getMethod()][0];

			$token = (isset($request->getHeaders()["HTTP_AUTHORIZATION"][0])) ? $request->getHeaders()["HTTP_AUTHORIZATION"][0] : "";
			$aclResourceName = str_replace("-", "", strtolower($module."_".$resource));

			if (!self::isUserLoggedIn($token))
			{
				$globalResponse = [];

				$globalResponse["status"] = 401;
				$globalResponse["body"]["errorStatus"] = true;
				$globalResponse["body"]["errorMessage"] = "You haven't been logged in or your supplied login token is invalid.";

				return $response->withJson($globalResponse["body"], $globalResponse["status"]);
			}
			
			return $next($request, $response);
		};
	}
}
