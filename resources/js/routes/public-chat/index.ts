import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PublicChatController::__invoke
* @see app/Http/Controllers/PublicChatController.php:12
* @route '/bantuan/chat'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/bantuan/chat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PublicChatController::__invoke
* @see app/Http/Controllers/PublicChatController.php:12
* @route '/bantuan/chat'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicChatController::__invoke
* @see app/Http/Controllers/PublicChatController.php:12
* @route '/bantuan/chat'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PublicChatController::__invoke
* @see app/Http/Controllers/PublicChatController.php:12
* @route '/bantuan/chat'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PublicChatController::__invoke
* @see app/Http/Controllers/PublicChatController.php:12
* @route '/bantuan/chat'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const publicChat = {
    store: Object.assign(store, store),
}

export default publicChat