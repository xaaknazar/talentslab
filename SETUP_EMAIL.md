# Настройка почтового сервера для TalentsLab

## 📋 Пошаговая инструкция

### Шаг 1: Подключение к серверу

```bash
ssh ubuntu@77.243.80.248
```

### Шаг 2: Установка Postfix (почтовый сервер)

```bash
# Обновляем систему
sudo apt update

# Устанавливаем Postfix
sudo apt install postfix -y

# Во время установки выберите:
# 1. "Internet Site" (сайт в интернете)
# 2. System mail name: talentslab.org
```

### Шаг 3: Настройка Postfix

Отредактируйте файл конфигурации:

```bash
sudo nano /etc/postfix/main.cf
```

Найдите и измените следующие параметры:

```conf
# Основные настройки
myhostname = talentslab.org
mydomain = talentslab.org
myorigin = $mydomain

# Откуда принимать письма
inet_interfaces = loopback-only

# Для кого доставлять почту
mydestination = localhost

# Размер сообщения (10MB)
message_size_limit = 10485760

# Сеть
mynetworks = 127.0.0.0/8 [::ffff:127.0.0.0]/104 [::1]/128

# Перезапись адресов
smtp_generic_maps = hash:/etc/postfix/generic
```

Создайте файл для перезаписи адресов:

```bash
sudo nano /etc/postfix/generic
```

Добавьте:
```
www-data@talentslab.org noreply@talentslab.org
ubuntu@talentslab.org noreply@talentslab.org
```

Обновите базу данных и перезапустите:

```bash
sudo postmap /etc/postfix/generic
sudo systemctl restart postfix
```

### Шаг 4: Установка OpenDKIM (для подписи писем)

```bash
# Установка
sudo apt install opendkim opendkim-tools -y

# Создаем директорию для ключей
sudo mkdir -p /etc/opendkim/keys/talentslab.org
cd /etc/opendkim/keys/talentslab.org

# Генерируем ключи
sudo opendkim-genkey -s mail -d talentslab.org

# Устанавливаем правильные права
sudo chown -R opendkim:opendkim /etc/opendkim
sudo chmod 600 /etc/opendkim/keys/talentslab.org/mail.private
```

### Шаг 5: Настройка OpenDKIM

Редактируем основной конфиг:

```bash
sudo nano /etc/opendkim.conf
```

Добавьте/измените:

```conf
# Основные настройки
Syslog yes
SyslogSuccess yes
LogWhy yes

# Подписывание
Canonicalization relaxed/simple
Mode sv

# Ключи и домены
Domain talentslab.org
Selector mail
KeyFile /etc/opendkim/keys/talentslab.org/mail.private

# Сокет
Socket inet:8891@localhost

# Доверенные хосты
InternalHosts /etc/opendkim/TrustedHosts
```

Создайте файл доверенных хостов:

```bash
sudo nano /etc/opendkim/TrustedHosts
```

Добавьте:
```
127.0.0.1
localhost
talentslab.org
*.talentslab.org
77.243.80.248
```

### Шаг 6: Интеграция OpenDKIM с Postfix

```bash
sudo nano /etc/postfix/main.cf
```

Добавьте в конец файла:

```conf
# OpenDKIM
milter_default_action = accept
milter_protocol = 6
smtpd_milters = inet:localhost:8891
non_smtpd_milters = $smtpd_milters
```

### Шаг 7: Перезапуск сервисов

```bash
sudo systemctl restart opendkim
sudo systemctl restart postfix

# Проверяем статус
sudo systemctl status opendkim
sudo systemctl status postfix
```

### Шаг 8: Получение DKIM ключа для DNS

```bash
sudo cat /etc/opendkim/keys/talentslab.org/mail.txt
```

Вы увидите что-то вроде:
```
mail._domainkey	IN	TXT	( "v=DKIM1; h=sha256; k=rsa; "
	  "p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC..." )
```

---

## 🌐 Настройка DNS записей

Зайдите в панель управления DNS вашего домена и добавьте:

### 1. SPF запись

```
Тип: TXT
Имя: @
Значение: v=spf1 a mx ip4:77.243.80.248 ~all
TTL: 3600
```

### 2. DKIM запись

```
Тип: TXT
Имя: mail._domainkey
Значение: v=DKIM1; h=sha256; k=rsa; p=ВАША_ПУБЛИЧНАЯ_ЧАСТЬ_КЛЮЧА
TTL: 3600
```

(Скопируйте значение из файла mail.txt, убрав кавычки и объединив строки)

### 3. DMARC запись

```
Тип: TXT
Имя: _dmarc
Значение: v=DMARC1; p=quarantine; rua=mailto:admin@talentslab.org; pct=100; adkim=s; aspf=s
TTL: 3600
```

### 4. MX запись (если её нет)

```
Тип: MX
Имя: @
Значение: talentslab.org
Приоритет: 10
TTL: 3600
```

---

## ⚙️ Настройка Laravel (.env файл)

На сервере отредактируйте файл:

```bash
cd /var/www/www-root/data/www/talentslab.org
nano .env
```

Измените параметры почты:

```env
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=25
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@talentslab.org
MAIL_FROM_NAME="TalentsLab"
```

Очистите кэш Laravel:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🧪 Тестирование

### 1. Отправьте тестовое письмо

```bash
echo "Test email from TalentsLab" | mail -s "Test Subject" your-email@gmail.com
```

### 2. Проверьте логи

```bash
# Логи Postfix
sudo tail -f /var/log/mail.log

# Логи OpenDKIM
sudo tail -f /var/log/syslog | grep opendkim
```

### 3. Онлайн проверка

Отправьте письмо на: check-auth@verifier.port25.com

Вы получите детальный отчет о SPF, DKIM и DMARC.

Также проверьте домен на:
- https://mxtoolbox.com/SuperTool.aspx (введите talentslab.org)
- https://www.mail-tester.com/ (отправьте письмо на указанный адрес)

---

## 🔧 Решение проблем

### Письма всё равно в спаме?

1. **Проверьте IP адрес в черных списках:**
   ```bash
   # Проверить можно тут:
   https://mxtoolbox.com/blacklists.aspx
   ```

2. **Проверьте reverse DNS (PTR запись):**
   Обратитесь к вашему хостинг-провайдеру и попросите настроить PTR запись:
   ```
   77.243.80.248 → talentslab.org
   ```

3. **Прогрев IP адреса:**
   Начните отправлять письма небольшими порциями и постепенно увеличивайте объем.

4. **Содержание писем:**
   - Избегайте слов-триггеров (бесплатно, акция, выиграй)
   - Добавьте ссылку на отписку
   - Используйте текстовую и HTML версии письма

### Проверка статуса служб

```bash
# Проверка Postfix
sudo systemctl status postfix
sudo postfix check

# Проверка OpenDKIM
sudo systemctl status opendkim
sudo opendkim-testkey -d talentslab.org -s mail -vvv
```

---

## 📊 Мониторинг

Следите за логами регулярно:

```bash
# Просмотр последних писем
sudo tail -100 /var/log/mail.log

# Отслеживание в реальном времени
sudo tail -f /var/log/mail.log
```

---

## ✅ Чек-лист

- [ ] Postfix установлен и настроен
- [ ] OpenDKIM установлен и настроен
- [ ] DNS записи добавлены (SPF, DKIM, DMARC, MX)
- [ ] PTR запись настроена (через хостинг-провайдера)
- [ ] Laravel .env обновлен
- [ ] Кэш Laravel очищен
- [ ] Тестовое письмо отправлено
- [ ] Письмо проверено на mail-tester.com (оценка 10/10)
- [ ] IP не в черных списках

---

## 📞 Помощь

Если возникнут проблемы на любом этапе, дайте знать!
