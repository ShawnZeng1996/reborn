var CACHE_NAME = 'reborn-service-worker';

// 在 fetch 事件中处理网络请求并缓存资源
self.addEventListener('fetch', function (event) {
    event.respondWith(
        caches.match(event.request).then(function (response) {
            // 如果缓存中有匹配的资源，直接返回
            if (response) {
                return response;
            }

            // 检查请求的 URL 是否匹配 emoji/xiaodianshi 或 emoji/wechat 文件夹下的图片
            var requestUrl = new URL(event.request.url);
            var imageRegex = /\/usr\/themes\/reborn\/assets\/emoji\/(xiaodianshi|wechat)\/.*\.(png|jpg|jpeg|gif|svg)$/;

            if (imageRegex.test(requestUrl.pathname)) {
                // 如果请求匹配指定目录下的图片文件，则进行缓存处理
                return fetch(event.request).then(function (httpRes) {
                    // 如果请求成功，缓存响应
                    if (!httpRes || httpRes.status !== 200 || request.method === 'POST') {
                        return httpRes;
                    }

                    var responseClone = httpRes.clone();
                    caches.open(CACHE_NAME).then(function (cache) {
                        cache.put(event.request, responseClone);
                    });

                    return httpRes;
                });
            }

            // 对于非匹配的请求，直接通过网络获取，不缓存
            return fetch(event.request);
        })
    );
});
