import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PublicChatController::__invoke
* @see app/Http/Controllers/PublicChatController.php:12
* @route '/bantuan/chat'
*/
const PublicChatController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PublicChatController.url(options),
    method: 'post',
})

PublicChatController.definition = {
    methods: ["post"],
    url: '/bantuan/chat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PublicChatController::__invoke
* @see app/Http/Controllers/PublicChatController.php:12
* @route '/bantuan/chat'
*/
PublicChatController.url = (options?: RouteQueryOptions) => {
    return PublicChatController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicChatController::__invoke
* @see app/Http/Controllers/PublicChatController.php:12
* @route '/bantuan/chat'
*/
PublicChatController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PublicChatController.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PublicChatController::__invoke
* @see app/Http/Controllers/PublicChatController.php:12
* @route '/bantuan/chat'
*/
const PublicChatControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: PublicChatController.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PublicChatController::__invoke
* @see app/Http/Controllers/PublicChatController.php:12
* @route '/bantuan/chat'
*/
PublicChatControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: PublicChatController.url(options),
    method: 'post',
})

PublicChatController.form = PublicChatControllerForm

export default PublicChatController