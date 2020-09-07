# Задание: Поиск кратчайшего пути в графе.

## Тестовое задание выполнил

Сибиряков Виктор  
Почта: dcrulez9601@gmail.com  
Telegram: +7(939)303-38-40

## Конфигурация

* В корне проекта выполнить ```composer install```.
* Указать в config/db.php корректные данные для авторизации в базе данных (в разработке использовалась СУБД PostgreSQL).
* Восстановить базу данных со схемами с помощью файла 'db_copy.sql' (копия делалась способом, описанным здесь: https://nicolaswidart.com/blog/duplicate-a-postgresql-schema, можно восстановить как написано там же в разделе 3, или просто набрать в терминале в корне директории ```psql -U username -d database_name -f db_copy.sql```). 
* Указать корневой директорией веб-сервера директорию 'web' данного проекта.

## Первая часть: REST API

### Требования к идентификаторам

* 'имя_графа': последовательность из символов 'a-z', 'A-Z', '0-9', '\_' длиной не менее 6 и не более 20 символов;
* 'идентификатор_вершины': положительное число не более 32767;
* 'вес_ребра': положительное число не более 2147483647;

### Пример работы

Для тестирования работы разработанного API использовался сервис https://reqbin.com

#### Создание графа

Отправить HTTP-запрос с методом POST на _localhost/graphs_, содержащий JSON с следующими полями 
* 'name': имя графа. 

Пример:

![graph_create](readme_images/graph_create.png)

Результат:

![graph_create_result](readme_images/graph_create_result.png)

#### Удаление графа

Отправить HTTP_запрос с методом DELETE на _localhost/graphs/'имя_графа'

Пример:

![graph_delete](readme_images/graph_delete.png)

Результат:

![graph_delete_result](readme_images/graph_delete_result.png)

#### Добавление вершин

Отправить HTTP-запрос с методом POST на _localhost/vertices_, содержащий JSON с следующими полями: 
* 'graph_name': имя графа,
* 'id': идентификатор вершины.

Пример:

![vertex_create](readme_images/vertex_create.png)

Результат:

![vertex_create_result](readme_images/vertex_create_result.png)

#### Удаление вершин

Отправить HTTP_запрос с методом DELETE на _localhost/vertices/'имя_графа','идентификатор_вершины'

Пример:

![vertex_delete](readme_images/vertex_delete.png)

Результат:

![vertex_delete_result](readme_images/vertex_delete_result.png)

#### Добавление ребер

Отправить HTTP-запрос с методом POST на _localhost/edges_, содержащий JSON с следующими полями: 
* 'graph_name': имя графа, 
* 'start_vertex_id': идентификатор вершины, в которой начинается ребро,
* 'end_vertex_id': идентификатор вершины, в которой заканчивается ребро,
* 'weight': вес ребра.

Пример:

![edge_create](readme_images/edge_create.png)

Результат:

![edge_create_result](readme_images/edge_create_result.png)

#### Удаление ребер

Отправить HTTP_запрос с методом DELETE на _localhost/edges/'имя_графа','идентификатор_стартовой_вершины','идентификатор_конечной_вершины'_

![edge_delete](readme_images/edge_delete.png)

Результат:

![edge_delete_result](readme_images/edge_delete_result.png)

#### Изменение веса ребра

Отправить HTTP-запрос с методом PUT на _localhost/edges/'имя_графа','идентификатор_стартовой_вершины','идентификатор_конечной_вершины'_, содержащий JSON с следующими полями:
* 'weight': вес ребра.

Пример:

![edge_update_weight](readme_images/edge_update_weight.png)

Результат:

![edge_update_weight_result](readme_images/edge_update_weight_result.png)

#### Поиск кратчайшего пути

Отправить HTTP-запрос с методом GET на _localhost/graphs/'имя_графа'/shortest_path/'идентификатор_стартовой_вершины','идентификатор_конечной_вершины'_ 

![shortest_path](readme_images/shortest_path.png)

Пример успешного результат:

![shortest_path_result_success](readme_images/shortest_path_result_success.png)

Пример результата, когда искомого пути по графу не существует:

![shortest_path_result_fail](readme_images/shortest_path_result_fail.png)

## Вторая часть: API для совместного редактирования

Прием изменений из одного браузера и отображение их в другом браузере (realtime).

### Запуск
* В терминале в корне проекта выполнить: ```php yii graph/start```.
* Открыть в браузере главную страницу (для локального хоста открыть ```http://localhost/```)

### Пример

![realtime_api_example](readme_images/realtime_api_example.png)
