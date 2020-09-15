# Aba Debug

Nesta aba, você poderá filtrar os *logs* por data e *níveis*.

![Aba debug](./assets/tab-debug.png#zoom)

# Gerenciar *logs*

![Botões para gerenciar logs](./assets/tab-debug-2.png)

**Limpar *log*:** O botão vermelho é responsável por remover o arquivo de *log*. Esta opção não poderá ser desfeita.

**Download do *Log*:** O botão verde é responsável por baixar os dados de *log* que aparecem na tela.

# Níveis do *log*

**DEBUG (100):** Informações detalhadas de debug.

**INFO (200):** Eventos interessantes. Exemplos: User logs in, SQL logs.

**NOTICE (250):** Eventos normais, mas significativos.

**WARNING (300):** Ocorrências excepcionais que não são erros. Exemplos: uso de APIs obsoletas, uso inadequado de uma API, coisas indesejáveis ​​que não são necessariamente erradas.

**ERROR (400):** Erros de tempo de execução que não requerem ação imediata, mas normalmente devem ser registrados e monitorados.

**CRITICAL (500):** Condições críticas. Exemplo: componente do aplicativo indisponível, exceção inesperada.

**ALERT (550):** A ação deve ser tomada imediatamente. Exemplo: site inteiro fora do ar, banco de dados indisponível, etc. Isso deve acionar os alertas de SMS e acordá-lo.

**EMERGENCY (600):** Emergência: o sistema está inutilizável.