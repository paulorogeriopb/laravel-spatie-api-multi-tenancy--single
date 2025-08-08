module.exports = {
    types: [
        { value: "feat", name: "✨ feat:     Nova funcionalidade" },
        { value: "fix", name: "🐛 fix:      Correção de bug" },
        { value: "docs", name: "📝 docs:     Documentação" },
        {
            value: "style",
            name: "💄 style:    Estilo/formatacao (espaços, ponto e vírgula, etc)",
        },
        { value: "refactor", name: "🔨 refactor: Refatoração de código" },
        { value: "test", name: "🧪 test:     Testes automatizados" },
        {
            value: "chore",
            name: "🧹 chore:    Tarefas auxiliares e manutenção",
        },
    ],
    messages: {
        type: "Selecione o tipo de alteração que você está submetendo:",
        scope: "Indique o escopo da alteração (opcional):",
        customScope: "Defina um escopo personalizado:",
        subject: "Escreva uma descrição curta e objetiva:",
        body: "Descreva a mudança detalhadamente (opcional):",
        breaking: "Descreva mudanças que quebram a compatibilidade (opcional):",
        footer: "Referencie os issues fechados (ex: #123) (opcional):",
        confirmCommit: "Confirma o commit acima?",
    },
    allowCustomScopes: true,
    allowBreakingChanges: ["feat", "fix"],
};
