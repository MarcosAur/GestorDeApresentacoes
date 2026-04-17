## Check-in via QR Code (Task 4)

### Added
- Implementação do componente Livewire `CheckinScanner` para gestão de credenciamento via hash manual ou QR Code.
- Nova rota administrativa `/checkin` (`admin.checkin`) para acesso ao painel de scanner.
- Link "Check-in" adicionado ao menu lateral (sidebar) para usuários com perfil de Admin.
- Geração automática de `qr_code_hash` (UUID) no `PresentationService` ao criar uma nova inscrição.
- Visualização de QR Code no painel do competidor (`EnrollmentPanel`) para inscrições com status "APTO".
- Novos componentes Blade em `resources/views/components/admin/` para suporte à interface do scanner.
- Suíte de testes funcionais em `tests/Feature/CheckinTest.php` cobrindo fluxos de sucesso, erro e check-in duplicado.

### Changed
- Atualização do painel de inscrições do competidor para exibir feedback visual quando o check-in ainda não está disponível.
