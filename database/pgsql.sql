-- 项目表
CREATE SEQUENCE projects_id_seq;
CREATE TABLE "public"."projects" (
  "id" int4 NOT NULL DEFAULT nextval('projects_id_seq'::regclass),
  "name" varchar(50) COLLATE "pg_catalog"."default",
  "created_at" timestamp(6),
  "updated_at" timestamp(6),
  CONSTRAINT "projects_pkey" PRIMARY KEY ("id")
);
ALTER TABLE "public"."projects" OWNER TO "postgres";
CREATE UNIQUE INDEX "name" ON "public"."projects" USING btree (
  "name" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);
COMMENT ON TABLE "public"."projects" IS '项目表';
COMMENT ON COLUMN "public"."projects"."name" IS '项目名称';
COMMENT ON COLUMN "public"."projects"."created_at" IS '创建时间';
COMMENT ON COLUMN "public"."projects"."updated_at" IS '更新时间';


-- 日志表
CREATE SEQUENCE logs_id_seq;
CREATE TABLE "public"."logs" (
  "id" int8 NOT NULL DEFAULT nextval('logs_id_seq'::regclass),
  "project" varchar(30) COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::character varying,
  "type" int2 NOT NULL DEFAULT 1,
  "tags" jsonb NOT NULL,
  "content" text COLLATE "pg_catalog"."default" NOT NULL DEFAULT ''::text,
  "time" timestamp(6) NOT NULL,
  "search" tsvector GENERATED ALWAYS AS (to_tsvector('jiebacfg'::regconfig, content)) STORED,
  CONSTRAINT "logs_pkey" PRIMARY KEY ("id")
);
ALTER TABLE "public"."logs" OWNER TO "postgres";
CREATE INDEX "idx_search" ON "public"."logs" USING gin (
  "search" "pg_catalog"."tsvector_ops"
);
CREATE INDEX "project" ON "public"."logs" USING btree (
  "project" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);
CREATE INDEX "type" ON "public"."logs" USING btree (
  "type" "pg_catalog"."int2_ops" ASC NULLS LAST
);
COMMENT ON TABLE "public"."logs" IS '日志表';
COMMENT ON COLUMN "public"."logs"."project" IS '项目标识';
COMMENT ON COLUMN "public"."logs"."type" IS '日志类型';
COMMENT ON COLUMN "public"."logs"."tags" IS '标签';
COMMENT ON COLUMN "public"."logs"."content" IS '日志内容';
COMMENT ON COLUMN "public"."logs"."time" IS '日志时间';