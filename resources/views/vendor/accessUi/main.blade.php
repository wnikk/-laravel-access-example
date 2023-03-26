
    <script>
        document.addEventListener("DOMContentLoaded", function(e)
        {
            const firstPage = '{{ empty($available['rules'])?'owners':'rules' }}';

            accessUi.init(document.body, firstPage, {
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
                availableRules: {{ empty($available['rules'])?'false':'true' }},
                availableOwners: {{ empty($available['owners'])?'false':'true' }},
                availableInherit: {{ empty($available['inherit'])?'false':'true' }},
                availablePermission: {{ empty($available['permission'])?'false':'true' }},
                routeRules: {!! empty($routes['rules'])?'null':json_encode($routes['rules']) !!},
                routeOwners: {!! empty($routes['owners'])?'null':json_encode($routes['owners']) !!},
                routeInherit: {!! empty($routes['inherit'])?'null':json_encode($routes['inherit']) !!},
                routePermission: {!! empty($routes['permission'])?'null':json_encode($routes['permission']) !!},
            });
        });
    </script>
