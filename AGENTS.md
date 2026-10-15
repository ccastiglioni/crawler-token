# crawler-token

- Aplicam-se as permissões explícitas do usuário: alterações exigem `codar=true`; inspeção Git exige `git=true`. Não fazer commit, push ou deploy sem pedido.
- Esta base é local e de testes. Não conectar a outros projetos ou servidores.
- Todo bootstrap Laravel deve usar `APP_ENV=testing` e conexão MySQL explícita: `DB_HOST=mysql_test`, `DB_PORT=3306`, `DB_DATABASE=crawler_token_test`, `DB_CONNECTION=mysql`, sem URL/socket alternativo. Execute pelo Docker Compose, que fixa esses valores.
- Rodar testes com `docker compose exec -T app php artisan test --env=testing`. PHPUnit mantém o alvo MySQL e usa cache em memória/fila síncrona para isolamento.
- Não imprimir `.env`, senhas, chaves ou a configuração expandida do Compose. `.env.example` é público; `.env` é local.
- Preserve `LICENSE`, o documento `crawler-rvn.md` e a regra existente que o ignora. Consulte esse documento para requisitos do domínio; confirme contratos externos na documentação oficial.
- Reutilize serviços Laravel. Antes de criar métodos, examine contratos existentes; prefira tipos/DTOs a parâmetros array, fluxo linear e constantes/configurações para valores de domínio.
- Configuração inicial de ativos está em `config/watchdog.php`; símbolos configurados são candidatos a validar nas fontes, não evidência de disponibilidade atual.
- Esta entrega contém somente infraestrutura e heartbeat; coleta, persistência do domínio, outbox e alertas dependem de implementação posterior.
- Formatar somente PHP alterado com `vendor/bin/pint`; validar comportamento afetado. Não instalar ferramentas adicionais automaticamente.
